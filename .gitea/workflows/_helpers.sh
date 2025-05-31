#!/bin/bash

VERSION=1-script
REPORTING_URL=https://techbox.stage.e115.com/cicd/upload

badFunction() {
    chmod -x abcde.txt # bad call, will cause _helpers.sh to exit with non-zero
    echo "haha"
    ls # good call
}

showDump() {
    echo "${yellow}# showDump${normal}"
    echo "${green}- Dumping all info for debugging${normal}"

    composer show --self --installed | grep avexsoft
    # composer why-not avexsoft/saas 10.0.54

    echo "${green}- Showing contents of /env.json${normal}"
    echo "$(cat /env.json)"

    echo "${green}- Showing contents of github.json${normal}"
    echo "$(cat github.json)"
}

setupTestEnvironment() {
    red=$(tput setaf 1)
    green=$(tput setaf 2)
    yellow=$(tput setaf 3)
    blue=$(tput setaf 4)
    purple=$(tput setaf 5)
    normal=$(tput setaf 7)

    # overwrite with the internal IP of our reporting hostnames
    # echo "192.168.214.10 satis.avexcode.com" >>/etc/hosts
    # echo "192.168.214.10 techbox.stage.e115.com" >>/etc/hosts
    # echo "192.168.214.10 techbox10.dv1.e115.com" >>/etc/hosts
    # echo "192.168.214.10 techbox10.dv3.e115.com" >>/etc/hosts

    # ping techbox10.dv1.e115.com
    # exit 1
    echo Script version: ${VERSION}
    curl --version
    php -v

    echo "${green}Saving GITHUB into github.json${normal}"
    echo $GITHUB >github.json
    COMMIT_MSG=$(jq -r .event.head_commit.message <github.json)

    echo ${yellow}${EVENT}${normal} from ${yellow}${ACTOR}${normal}: ${yellow}$COMMIT_MSG${normal}
    echo "${green}- Branch is ${BRANCH} of ${REPO}"
    echo "${green}- Logs at ${URL}${REPO}/actions/runs/${RUN_ID}"

    echo "${green}- Saving ENV into temp1.json${normal}"
    jq -n '$ENV' >temp1.json

    # create env.json
    echo "${green}- Merging temp1 and github${normal}"
    jq '.GITHUB = [input]' temp1.json github.json >/env.json

    /usr/sbin/mysqld --user=root &

    # block until mysqld is ready
    mysqladmin --silent --wait=30 ping || exit 1
    echo "${green}- MySQL is now running${normal}"

    # showDump # use only when trying to debug

    preventModifiedMigrations
}

preventModifiedMigrations() {
    # "allow_modified_migrations": true,
    is_allowed=$(jq -r .extra.laravel.allow_modified_migrations <composer.json)
    if [ $is_allowed == true ]; then
        echo "- Modified migrations are allowed"
    else
        echo "- Modified migrations are ${red}NOT ALLOWED${normal}, action will fail if there are modifications"

        LAST_TAG=$(git describe --abbrev=0 --tags)
        # check between last tag and latest commit for (M)odified or (D)eleted
        ! git diff --name-status $LAST_TAG HEAD | grep -q "^M\sdatabase/migrations/.*"
        ! git diff --name-status $LAST_TAG HEAD | grep -q "^D\sdatabase/migrations/.*"
    fi
}

setupDependenciesWithComposer() {
    if [[ -f "artisan" ]]; then
        echo "${yellow}- Laravel project as artisan exists, creating .env and folders${normal}"
        if [[ -f ".env.testing" ]]; then
            # new
            cp .env.testing .env
        else
            # old
            cp .env.test .env
        fi

        mkdir -p storage/framework/sessions
        mkdir -p storage/framework/views
        mkdir -p storage/framework/cache
    else
        echo "${yellow}- Laravel package as artisan does not exist${normal}"
    fi

    # fix composer.json existence OR fix repositories
    if [ -f composer-prod.json ]; then
        # < v1.0.6 handling
        rm -f composer.json
        ln -s composer-prod.json composer.json
    else
        # v1.0.6 handling create production composer.json by modifying repositories
        echo "${yellow}- Pulling packages from only https://satis.avexcode.com${normal}"
        jq -c 'del(.repositories) + { "repositories": [
                  {
                      "type": "composer",
                      "canonical": false,
                      "url": "https://satis.avexcode.com",
                  }
              ]}' composer.json >tmp.$$.json
        rm composer.json
        mv tmp.$$.json composer.json
    fi

    # ensure pest plugins can run
    jq -c '.config += {
        "allow-plugins": {
            "pestphp/pest-plugin": true
        }}
    ' composer.json >tmp.$$.json
    rm composer.json
    mv tmp.$$.json composer.json

    # unset COMPOSER_AUTH
    if [[ -f composer.lock ]]; then
        rm composer.lock
    fi

    composer config http-basic.satis.avexcode.com token ${TOKEN}
    composer update
    composer show
}

setupIfLaravelProject() {
    if [[ -f "artisan" ]]; then
        echo "${green}- Laravel project, ${yellow}running artisan${green} commands${normal}"

        # run these so that artisan commands can run properly
        php artisan key:generate
        php artisan migrate:fresh --seed

        php artisan optimize:clear
        php artisan schedule:clear-cache
        php artisan auth:clear-resets
        php artisan vendor:publish --tag laravel-assets --ansi --force

    else
        echo "${green}- Laravel package, ${yellow}not running artisan${green} commands${normal}"
    fi
}

runPhpUnitTests() {

    if [[ -f ".usepest" ]]; then
        echo "${green}Pest ${yellow}${normal}"
        ./vendor/bin/pest --testdox --log-events-text /phpunit-events.txt
    else
        # this regex can catch "10.4-dev"
        export phpunit_full=$(./vendor/bin/phpunit --version | grep -Eo '[0-9]+\.[.0-9]+')
        export phpunit_major=$(echo $phpunit_full | cut -d. -f1)
        export IS_RUNNER=1

        echo "${green}PHPUnit ${yellow}${phpunit_major}${normal}"
        if ((phpunit_major > 9)); then
            # version >=10

            touch /phpunit-events.txt
            # ./vendor/bin/phpunit --testdox --log-events-verbose-text /phpunit-events.txt
            ./vendor/bin/phpunit --testdox --log-events-text /phpunit-events.txt
            # > test ; cat test ; cat test | grep "│\|✘"
        else
            # version <=9

            touch /phpunit-testdox.xml
            ./vendor/bin/phpunit --testdox --testdox-xml /phpunit-testdox.xml
        fi
    fi
}

revertIfAnyStepFailed() {
    showDump

    if # if an update is needed, don't revert
        [[ -f "/need-update" ]]
    then
        exit 0
    fi

    if [ "$SHA" != "$(git rev-parse HEAD)" ]; then
        TAG=$(git describe --abbrev=0 --tags)
        echo "${green}Reverting to last good tag [${yellow}$TAG${green}] and pushing${normal}"
        git reset --hard $TAG
        git push origin $URL --force
    fi
}

tagAndPublish() {
    git config credential.${URL}.helper "!f() { sleep 1; echo \"username=token\"; echo \"password=${TOKEN}\"; }; f"
    # next step will always have exit code of 0
    git config --unset http.${URL}.extraheader || true

    git fetch

    # tag with a version
    curl --fail-with-body --location -O https://token:${TOKEN}@avexcode.com/avexsoft/dev-tools/raw/devops/tag-from-commit.sh
    chmod +x tag-from-commit.sh
    ./tag-from-commit.sh
    git push origin ${REPO_URL} --tags

    echo "${green}Default branch is [${yellow}${DEFAULT_BRANCH}${green}]"
    echo "${green}Current branch is [${yellow}${REPO_URL}${green}]"

    # if default branch, then fast forward master
    if [ "${DEFAULT_BRANCH}" = "${REPO_URL}" ]; then
        echo "Fast forwarding"
        git checkout master --force
        git merge --ff-only ${REPO_URL}
        git push origin
    fi

    # publish
    echo "${green}Publishing via REPORTING_URL: ${yellow}${REPORTING_URL}/publish${green}"
    curl \
        -F "action=publish" \
        -F "version=<tagged.version" \
        -F "json=</env.json" \
        --fail-with-body \
        ${REPORTING_URL}/publish
}

notifyWatchers() {
    # if an update is needed, don't notify
    if [[ -f "/need-update" ]]; then
        echo "Updating action YAML file, no need to inform watchers"
        clearFiles
        exit 0
    fi

    if [[ ! -f /phpunit-testdox.xml ]] && [[ ! -f /phpunit-events.txt ]]; then
        touch /phpunit-testdox.xml
    fi

    if [[ -f /phpunit-events.txt ]]; then
        ls -l /phpunit-events.txt
        echo "${green}Notifying via REPORTING_URL: ${yellow}${REPORTING_URL}/notify${green} of ${yellow}phpunit-events.txt${green} results${normal}"
        curl \
            -F "action=notify" \
            -F "json=</env.json" \
            -F "files[]=@/phpunit-events.txt" \
            --fail-with-body \
            ${REPORTING_URL}/notify
    else
        ls -l /phpunit-testdox.xml
        echo "${green}Notifying via REPORTING_URL: ${yellow}${REPORTING_URL}/notify${green} of ${yellow}phpunit-testdox.xml${green} results${normal}"
        curl \
            -F "action=notify" \
            -F "json=</env.json" \
            -F "files[]=@/phpunit-testdox.xml" \
            --fail-with-body \
            ${REPORTING_URL}/notify
    fi

    clearFiles
}

clearFiles() {
    if [ -f /env.json ]; then
        rm /env.json
    fi
    if [ -f tagged.version ]; then
        rm tagged.version
    fi
    if [ -f github.json ]; then
        rm github.json
    fi
}

red=$(tput setaf 1)
green=$(tput setaf 2)
yellow=$(tput setaf 3)
blue=$(tput setaf 4)
purple=$(tput setaf 5)
normal=$(tput sgr0)

# from https://unix.stackexchange.com/a/308314/566307,
# `set -e` will cause this script to break upon any errors (commands that return non-zero)
set -e
$1 "$@"
exit 0
