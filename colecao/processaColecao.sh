#!/bin/bash

source conf.sh

if [ ! -d $OUTPUT ]; then
	mkdir $OUTPUT >/dev/null 2>&1
	# $? é o valor de retorno do último comando executado. 
	# Se for diferente de 0, houve algum problema.
	if [ $? -ne 0 ]; then
		echo "ERRO - não foi possível criar a pasta '$OUTPUT'"
		exit
	fi
fi
php php/parser.php "$SOURCES" "$LINKS" "$OUTPUT"
