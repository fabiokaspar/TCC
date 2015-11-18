<!-- Change your life! Don't waste time looking for! Join us in ... LookingFor -->
<html>
  <head>
    <title>LookingFor</title>
    <link rel="stylesheet" type="text/css" href="css/regras.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry&language=pt-BR"></script>
    <script src="js/markerWithLabel.js"></script>
    <script src="js/mapa.js" type="text/javascript"></script>
    <script src="js/sParameters.js" type="text/javascript"></script>
    <meta charset="utf-8">
  </head>
  <body>
  <div id="conteudo">  
      <div id="titulo">LookingFor</div>
    	<!-- *************************** -->
        
        <div id="corpo">
            <table style="height:100%">
                <tr style="height:30px">
                    <td colspan="2" id="pesquisa" align='center'>
                        <br><br><br><br>
                        <form method="POST" id="ajax_form">     

                            <table id="query">
                                <tr>
                                    <td width="100%">
                                        <input id="estab" type="text" placeholder="Estabelecimento..." 
                                            class="focus" name="query">
                                    </td>
                                    <td align="right">
                                        <button id="btnPesquisar">Pesquisar</button><br>
                                    </td>
                                </tr>
                            </table>
                            <div id='opcoes'>
                                <div>
                                    Ordenar por:
                                </div>
                                <div class='param metallic hover' data-value="endereco" data-str="distância">
                                    <span class="order"></span>
                                    Distância
                                </div>
                                <div class='param metallic hover' data-value="preco" data-str="preço">
                                    <span class="order"></span>
                                    Preço
                                </div>
                                <div class='param metallic hover' data-value="nota" data-str="qualidade">
                                    <span class="order"></span>
                                    Qualidade
                                </div>
                            </div>
                        </form>                
                    </td>
                </tr>
                <!-- *************************** -->
                <tr>
                    <td>
                        <div id="googleMap"></div>
                    </td>
                    <td width='300'>
                        <div class='rotulo metallic'>Resultados</div>
                        <div id="espera" style="display: none;text-align: center">
                            <br/><b>Carregando...</b>
                            <img src="images/loading.gif" alt="">
                        </div>
                        <div id="mensagem"></div>
                        <form id="restaurantes">
                            <b style="display:none">Lista de restaurantes<span id="parametrosContainer"> por <span id="parametrosBuscados">Distância</span></span>:<br></b>
                            <div class="info">
                                Escolha um dos estabelecimentos para calcular a rota:
                            </div>
                            <div id="listaRestaurantes"></div>
                            <input type='button' id='btnRota' value='Criar rota até o local marcado'>
                        </form>
                    </td>
                </tr>
            </table>
            <br/><br/>
        </div>
        <div id="rodape">
            Criado por: Fábio Eduardo Kaspar, Igor Canko Minotto e Ricardo Oliveira Teles (2015)
        </div>
   </div>
  </body>
</html>
<?php include_once("analyticstracking.php") ?>