#!/bin/bash

path=../lucene-5.1.0/core/lucene-core-5.1.0.jar
path=$path:../lucene-5.1.0/queryparser/lucene-queryparser-5.1.0.jar
path=$path:../core.jar
#path=$path:../lucene-5.1.0/demo/lucene-demo-5.1.0.jar
path=$path:../lucene-5.1.0/analysis/common/lucene-analyzers-common-5.1.0.jar

pathIndex=./restaurantes/index

query=$1;shift
lat='-23.5505199';
lon='-46.6333094';
params='';
while test $# -gt 0; do
	case "$1" in
    -lat)
			shift
			lat=$1
			shift
			;;
		-lon)
			shift
			lon=$1
			shift
			;;
		-distance)
			params="$params $1"
			shift
			;;
		-price)
			params="$params $1"
			shift
			;;
		-grade)
			params="$params $1"
			shift
			;;
		*)
			echo "Argumento '$1' é inválido!"
			exit
			;;
	esac
done
#java -classpath $path org.apache.lucene.demo.SearchFiles -query "$1" -paging 50 -index $pathIndex
java -classpath $path core.SearchFiles -query "$query" -paging 50 -lat "$lat" -lng "$lon" $params
