#!/bin/sh
mkdir tmp
cd tmp
wget http://dl.google.com/closure-compiler/compiler-latest.zip
unzip compiler-latest.zip
mv compiler.jar /bin/closure-compiler.jar -f -v
chmod 0777 /bin/closure-compiler.jar
cd ..