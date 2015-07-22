#!/bin/bash

pegaLinkDosSites(){
	## salva as 19 paginas do guia folha no diretorio com formato html
	i=1; num=1;
	# fim=901;
	fim=1;
	
	while [ $i -le $fim ]; do 
		wget -qO- http://guia1.folha.com.br/busca/restaurantes/?sr=$i > siteRestaurantes$num.html
		cat siteRestaurantes$num.html | egrep -o "http://.*\#onde" >> linksRestaurantes.txt
		i=$((i+50));
		num=$((num+1)); 
	done
	echo "Links dos sites dos restaurantes ......OK"
}

pegaNomeDosSites(){
	cat linksRestaurantes.txt | egrep -o "([A-Za-z]|_|[0-9])+\?" | egrep -o "([A-Za-z]|_|[0-9])+" > nomes.txt
	echo "Nome dos restaurantes .................OK"
}

criaPaginaRestaurante(){
	## cria um array com os nomes dos restaurantes
	for nome in $(cat nomes.txt); do
		arrayNome=("${arrayNome[@]}" $nome)
	done 
	
	## cria um array com os links dos restaurantes
	for link in $(cat linksRestaurantes.txt); do
		arrayLink=("${arrayLink[@]}" $link) 
	done 

	## acessa o link e coloca o seu conteudo no arquivo com o nome correspondente 
	i=0; 
	while [ $i -lt ${#arrayNome[*]} ]; do 
   	wget -qO-  ${arrayLink[$i]} > ${arrayNome[$i]}."txt"
	  # cat ${arrayNome[$i]}."txt" | egrep -o "<\!--TITLE-->*<\!--/LOCATIONS-->" > ${arrayNome[$i]}."txt"
		php ../cleaner.php ${arrayNome[$i]}."txt"
		i=$((i+1)); 
	done	
	echo "Arquivo de cada um dos restaurantes ...OK"
}

removeArquivosDesnecessarios(){
	rm *.html linksRestaurantes.txt nomes.txt
	echo "Arquivos desnecessarios removidos .....OK"
}

################ MAIN ################  

if [[ ! -e ./restaurante ]]; then
	mkdir ./restaurante
	cd restaurante
	
	pegaLinkDosSites
	pegaNomeDosSites
	criaPaginaRestaurante
	removeArquivosDesnecessarios
fi
