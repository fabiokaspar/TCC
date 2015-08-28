#!/bin/bash

source conf.sh

pegaLinkDosSites(){
	echo -ne "Links dos sites dos restaurantes ......"
	## salva as 19 paginas do guia folha no diretorio com formato html
	i=1; num=1;
	fim=944;
	if [ -e ${ROOT}/linksRestaurantes.txt ]; then 
		rm ${ROOT}/linksRestaurantes.txt
	fi
	while [ $i -le $fim ]; do 
			echo -ne \\r"Links dos sites dos restaurantes ......($num/19)"
		wget -qO- http://guia1.folha.com.br/busca/restaurantes/?sr=$i > ${ROOT}/siteRestaurantes$num.html
		cat ${ROOT}/siteRestaurantes$num.html | egrep -o "http://.*\#onde" >> ${ROOT}/linksRestaurantes.txt
		i=$((i+50));
		num=$((num+1)); 
	done
	echo -e \\r"Links dos sites dos restaurantes ......OK                "
}

pegaNomeDosSites(){
	echo -n "Nome dos restaurantes ................."
	cat ${ROOT}/linksRestaurantes.txt | egrep -o "([A-Za-z]|_|[0-9])+\?" | egrep -o "([A-Za-z]|_|[0-9])+" > ${ROOT}/nomes.txt
	echo "OK"
}

criaPaginaRestaurante(){
	echo -n "Arquivo de cada um dos restaurantes ..."
	## cria um array com os nomes dos restaurantes
	for nome in $(cat ${ROOT}/nomes.txt); do
		arrayNome=("${arrayNome[@]}" $nome)
	done 
	
	## cria um array com os links dos restaurantes
	for link in $(cat ${ROOT}/linksRestaurantes.txt); do
		arrayLink=("${arrayLink[@]}" $link) 
	done 

	## acessa o link e coloca o seu conteudo no arquivo com o nome correspondente 
	i=0; 
	max=${#arrayNome[*]}
	while [ $i -lt ${#arrayNome[*]} ]; do 
		echo -ne \\r"Arquivo de cada um dos restaurantes ...($i/$max)"
   	wget -qO-  ${arrayLink[$i]} > ${SOURCES}/${arrayNome[$i]}."html"
		echo ${arrayLink[$i]} > ${LINKS}/${arrayNome[$i]}."txt"
		i=$((i+1)); 
	done	
	echo -e \\r"Arquivo de cada um dos restaurantes ...OK           "
}

removeArquivosDesnecessarios(){
	echo -n "Arquivos desnecessarios removidos ....."
	rm ${ROOT}/*.html ${ROOT}/linksRestaurantes.txt ${ROOT}/nomes.txt
	echo "OK"
}

criaPasta(){
	if [ ! -d $1 ]
	then
		mkdir $1
	fi
}
################ MAIN ################  
	
criaPasta $ROOT
criaPasta $SOURCES
criaPasta $LINKS
#cd ./restaurantes
pegaLinkDosSites
pegaNomeDosSites
criaPaginaRestaurante
removeArquivosDesnecessarios
#cd ..
