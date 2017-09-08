#!/bin/bash

#参数: $1项目名称
if [ ! $1 ]
then
    echo '项目名称不能为空!'
    exit
fi

if [ !  -d "../$1" ]
then
    echo "项目$1目录不存在"
    exit
fi

cp -Rf admin/controllers  ../$1/admin
cp -Rf admin/tasks        ../$1/admin
#cp -Rf admin/public       ../$1/admin
cp -Rf framework          ../$1
cp -Rf interface/controllers   ../$1/interface
cp -Rf interface/tasks         ../$1/interface
#cp -Rf interface/public       ../$1/interface

cp -Rf libs   /tmp/
rm -rf /tmp/libs/common/apikeys
rm -rf /tmp/libs/common/appkeys
rm -rf /tmp/libs/common/webkeys
rm -rf /tmp/libs/common/Rsa.php
rm -rf /tmp/libs/plugins/admin/Elements.php
rm -rf /tmp/libs/plugins/VoltFilter.php
cp -Rf /tmp/libs ../$1
cp -Rf vendor    /tmp/
find /tmp/vendor/ -type d -name ".git" -exec rm -rf {} \;
cp -Rf /tmp/vendor ../$1
rm -rf /tmp/libs
rm -rf /tmp/vendor