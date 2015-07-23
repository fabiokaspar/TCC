#!/bin/bash

if [[ ! -e ./index ]]; then
	java org.apache.lucene.demo.IndexFiles -docs $PWD
fi

java org.apache.lucene.demo.SearchFiles