#!/bin/sh

if [ -e .git ]
then
    if [ -e lib/pdepend/PHP ]
    then
        cd lib/pdepend
        git fetch origin
        git rebase origin/master
        cd ../..
    else
        git clone git://github.com/manuelpichler/pdepend.git lib/pdepend
    fi;

    if [ -e setup ]
    then
        cd setup
        git fetch origin
        git rebase origin/master
        cd ..
    else
        git clone git://github.com/manuelpichler/build-commons.git setup
    fi;
fi;
