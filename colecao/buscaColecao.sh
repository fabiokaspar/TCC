#!/bin/bash

path=../lucene-5.1.0/core/lucene-core-5.1.0.jar
path=$path:../lucene-5.1.0/queryparser/lucene-queryparser-5.1.0.jar
#path=$path:../core.jar
path=$path:../lucene-5.1.0/demo/lucene-demo-5.1.0.jar
path=$path:../lucene-5.1.0/analysis/common/lucene-analyzers-common-5.1.0.jar

pathIndex=./restaurantes/index

java -classpath $path org.apache.lucene.demo.SearchFiles -query "$1" -paging 50 -index $pathIndex
