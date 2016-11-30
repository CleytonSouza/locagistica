<?php 
$this->breadcrumbs = array('Importe');
?>

<style>
.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}
</style>

<script>
$(function() {

  // We can attach the `fileselect` event to all file inputs on the page
  $(document).on('change', ':file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
  });

  // We can watch for our custom `fileselect` event like this
  $(document).ready( function() {
      $(':file').on('fileselect', function(event, numFiles, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

          if( input.length ) {
              input.val(log);
          } else {
              if( log ) alert(log);
          }

      });
  });
  
});
</script>

<script>
    $(".alert").alert('close')
</script>

<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="js/bootstrap-filestyle.js"></script>
 

    <!--<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>-->
   <!-- <script type="text/javascript" src="js/bootstrap-filestyle.min.js"> </script> -->


  </head>
<body>

<?php 
$this->breadcrumbs = array('Logistica');
?>


</br>
<br>
<div class="alert alert-warning">
   <strong>Importar Arquivo Retorno FM</strong>
</div>

<!-- Form -->
<form action="" method="post" enctype="multipart/form-data">

   <div class="col-xs-4">

         <span class="form-group">
              <input type="file" name="arquivo" class="filestyle" data-icon="false">
         </span>
   
   </div>
   

   <br><br>

 <!--  <input type="file" name="arquivo" > <br><br> -->
  <input type="submit" value="Enviar" name="Enviar" class= "btn btn-primary">
</form>




<?php

include 'conexao\conexao.php';


if(isset($_POST['Enviar']) && !isset($_FILES['arquivo'])){
?>
  
<div class="alert alert-error">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Nenhum arquivo Selecionado!</strong> 
</div>

<?php
}

else if(isset($_POST['Enviar'])) {

    // Configura o tempo limite para ilimitado
set_time_limit(0);




/*-----------------------------------------------------------------------------*
 * Parte 1: Configurações do Envio de arquivos via FTP com PHP
/*----------------------------------------------------------------------------*/

// IP do Servidor FTP
$servidor_ftp = '';
$port = '';

// Usuário e senha para o servidor FTP admin
$usuario_ftp = '';
$senha_ftp   = '';


// Extensões de arquivos permitidas
$extensoes_autorizadas = array('.csv');

// Caminho da pasta FTP
$caminho = '/Imp/';

/* 
Se quiser limitar o tamanho dos arquivo, basta colocar o tamanho máximo 
em bytes. Zero é ilimitado
*/
$limitar_tamanho = 0;

/* 
Qualquer valor diferente de 0 (zero) ou false, permite que o arquivo seja 
sobrescrito
*/
$sobrescrever = 0;




/*-----------------------------------------------------------------------------*
 * Parte 2: Configurações do arquivo
/*----------------------------------------------------------------------------*/

// Aqui o arquivo foi enviado e vamos configurar suas variáveis
$arquivo = $_FILES['arquivo'];

// Nome do arquivo enviado
$nome_arquivo = $arquivo['name'];

// Tamanho do arquivo enviado
$tamanho_arquivo = $arquivo['size'];

// Nome do arquivo temporário
$arquivo_temp = $arquivo['tmp_name'];

// Extensão do arquivo enviado
$extensao_arquivo = strrchr( $nome_arquivo, '.' );


// Exemplo de script para exibir os nomes obtidos no arquivo CSV de exemplo
 
$info = pathinfo($nome_arquivo);
$ext = $info['extension']; // get the extension of the file
$newname = 'E'.date('Ymdhis').'.csv'; 

$target = 'documentos/'.$newname;
move_uploaded_file( $arquivo_temp, $target);

// O destino para qual o arquivo será enviado
$destino = $caminho . $newname;

 
// Abrir arquivo para leitura 
$f = fopen($target, 'r'); 
if ($f) {
 
 fgetcsv($f, 1000, ";");

while (($data = fgetcsv($f, 1000, ";")) !== FALSE) {


  #$track_expo = "E".date("YmdHms").rand(1,50);
  $track_expo = utf8_encode($data[0]);
  #AWB
  $awb = utf8_encode($data[1]);
  #status 
  $status = utf8_encode($data[2]);
  #1º tentativa
  $tentativas = $data[3];
   #tentativa quantidade
  $tentativaquantidade = utf8_encode($data[4]);
  #Destinatario Bairro
  $quemrecebeu = utf8_encode($data[5]);
  #Destinatario Cidade
  $rg = utf8_encode($data[6]);
  #Destinatario Estado
  $motivo = utf8_encode($data[7]);
  #horachegada
  $datahoraentrega = utf8_encode($data[8]);
  

$query = "UPDATE easytracking.tb_espo  SET STATUS = '".$status."',  ts_entrega = '".$datahoraentrega."', tentativa = '".$tentativas."',  tentativaquantidade = '".$tentativaquantidade."', RECEBEDOR = '".$quemrecebeu."', rg_recebedor = '".$rg."', motivo_entrega = '".$motivo."'  WHERE  Track_expo = '".$track_expo."' ";


$result_coleta = mysql_query($query, $conecta) or print_r(mysql_error());



} #if while

fclose($f);
}



/*-----------------------------------------------------------------------------*
 *  Parte 3: Verificações do arquivo enviado
/*----------------------------------------------------------------------------*/

/* 
Se a variável $sobrescrever não estiver configurada, assumimos que não podemos 
sobrescrever o arquivo. Então verificamos se o arquivo existe. Se existir; 
terminamos aqui. 
*/




if ( ! $sobrescrever && file_exists( $destino ) ) {
  exit('Arquivo já existe.');
}


#Se a variável $limitar_tamanho tiver valor e o tamanho do arquivo enviado for
#maior do que o tamanho limite, terminado aqui.


if ( $limitar_tamanho && $limitar_tamanho < $tamanho_arquivo ) {
  exit('Arquivo muito grande.');
}

 
#Se as $extensoes_autorizadas não estiverem vazias e a extensão do arquivo não 
#estiver entre as extensões autorizadas, terminamos aqui.


if ( ! empty( $extensoes_autorizadas ) && ! in_array( $extensao_arquivo, $extensoes_autorizadas ) ) {
 // exit('Tipo de arquivo não permitido.');
  ?>
  <div class="alert alert-error">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Tipo de arquivo não permitido!</strong> 
</div>;


<?php
}
   

/*-----------------------------------------------------------------------------*
 * Parte 4: Conexão FTP
/*----------------------------------------------------------------------------*/



// Realiza a conexão
$conexao_ftp = ftp_connect( $servidor_ftp, $port );

// Tenta fazer login
$login_ftp = ftp_login( $conexao_ftp, $usuario_ftp, $senha_ftp );

// Se não conseguir fazer login, termina aqui
if ( ! $login_ftp ) {
  exit('Usuário ou senha FTP incorretos.');
}

ftp_pasv($conexao_ftp, true);

// Envia o arquivo
if ( ftp_put( $conexao_ftp, $destino, $target, FTP_ASCII ) ) {
  // Se for enviado, mostra essa mensagem
  ?>

<div class="alert alert-success">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Arquivo Salvo com Sucesso!
   <?
  print_r($query);
  die(); ?></strong> 
</div>



 <?php
} else {
  // Se não for enviado, mostra essa mensagem
  ?>

<div class="alert alert-error">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Erro ao enviar o arquivo!</strong> 
</div>;

<?php
}

// Fecha a conexão FTP
ftp_close( $conexao_ftp );

} 

?>
</body>
</html>
