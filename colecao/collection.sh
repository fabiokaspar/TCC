#!/bin/bash

if [[ ! -e ./restaurante ]]; then
	mkdir ./restaurante
	cd restaurante
	
	# # salva as 19 paginas do guia folha no diretorio com formato html
	# i=1; 
	# while [ $i -le 901 ]; do 
	# 	wget -qO- http://guia1.folha.com.br/busca/restaurantes/?sr=$i > siteRestaurantes$i.html
	# 	i=$((i+50)); 
	# done

	# salva o site do guia folha no diretorio com formato html
	wget -qO- http://guia1.folha.com.br/busca/restaurantes/?q= > siteRestaurantes.html
	# recupera os links dos restaurantes
	cat siteRestaurantes.html | egrep -o "http://.*\#onde" > linksRestaurantes.txt
	# recupera o nome de cada restaurante para criar um arquivo com esse nome
	cat linksRestaurantes.txt | egrep -o "([A-Za-z]|_)+#" | egrep -o "([A-Za-z]|_)+" > nomes.txt

	# cria um array com os nomes dos restaurantes
	for nome in $(cat nomes.txt); do
		arrayNome=("${arrayNome[@]}" $nome)
	done 
	
	# cria um array com os links dos restaurantes
	for link in $(cat linksRestaurantes.txt); do
		arrayLink=("${arrayLink[@]}" $link) 
	done 

	# acessa o link e coloca o seu conteudo no arquivo com o nome correspondente 
	i=0; 
	while [ $i -lt ${#arrayNome[*]} ]; do 
	   	wget -qO-  ${arrayLink[$i]} > ${arrayNome[$i]}."txt"
		i=$((i+1)); 
	done	

	rm siteRestaurantes.html linksRestaurantes.txt nomes.txt
fi
