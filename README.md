# TCC - LookingFor

####Lucene (para rodar no terminal)
Este passo só é necessário para rodar o Lucene via terminal. 

No caso do LookingFor, os scripts que chamam o Lucene já vem com a CLASSPATH setada logo no início
deles. 
Os scripts ignoram a configuração do ambiente do bash e a tarefa de setar as variáveis é manual.

#####Vamos lá então:                                                              
No arquivo ~/.bashrc (ele é um arquivo oculto, só aparece com ls -la),                                              adicionar (supondo que a pasta do lucene foi descompactada no ~/):

 `CLASSPATH=.:~/lucene-5.1.0/core/lucene-core-5.1.0.jar`  
 `CLASSPATH=$CLASSPATH:~/lucene-5.1.0/queryparser/lucene-queryparser-5.1.0.jar`  
 `CLASSPATH=$CLASSPATH:~/lucene-5.1.0/demo/lucene-demo-5.1.0.jar`  
 `CLASSPATH=$CLASSPATH:~/lucene-5.1.0/analysis/common/lucene-analyzers-common-5.1.0.jar`  
 `export CLASSPATH` 

Depois é só compilar os 2 arquivos da documentação do lucene **somente** com `javac arquivo.java` e rodar **exatamente** conforme o código sugere. 

######Site referencia para lista de estabelecimentos:

`http://guia1.folha.com.br/busca/restaurantes/?q`

####Iniciar o apache

`$ sudo service apache2 start`
