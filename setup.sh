#!/bin/sh

if [ -e .git ]
then
    git submodule add git://github.com/manuelpichler/pdepend.git lib/pdepend
    git submodule add git://github.com/manuelpichler/build-commons.git setup
fi;
