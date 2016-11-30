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
    <script type="text/javascript" src="js/bootstrap-filestyle.js"></script>
  </head>
<body>

<?php 
$this->breadcrumbs = array('Logistica');
?>

<?php
  $this->widget('bootstrap.widgets.TbButton', array(
    #'buttonType'=>'button',
    'size'=>'small',
    #'type'=>'danger',
    'type'=>'warning',
    'url'=>Yii::app()->baseUrl.'/modelos/Modelolayout.csv',
    'label'=>'Modelo Layout - (APENAS EXEMPLO)',
    'icon'=>'icon-refresh',
  ));
?>

</br>
<br>
<div class="alert alert-warning">
   <strong>Apenas extensão .csv</strong>
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

// Usuário e senha para o servidor FTP
#$usuario_ftp = 'gabriel.doria';
#$senha_ftp   = 'pitney1a';

// Extensões de arquivos permitidas
$extensoes_autorizadas = array('.csv');

// Caminho da pasta FTP
$caminho = '/FM/';

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

  #[8]
  $track_expo = utf8_encode($data[8]);
  #$track_expo = "E".date("YmdHms").rand(1,50);
  $cod_remessa = utf8_encode($data[2]);
  #Nome Destinatario
  $destnome = utf8_encode($data[14]);
  #Destinatario endereço 
  $local_entrega = utf8_encode($data[17]);
  #Destinatario Numero
  $numero = $data[18];
  #Destinatario Bairro
  $bairro = utf8_encode($data[21]);
  #Destinatario Cidade
  $cidade = utf8_encode($data[22]);
  #Destinatario Estado
  $uf = $data[23];
  #Destinatario CEP
  $cep = $data[25];
  #Destinatario Email
  $email = $data[26];

  if(!isset($email)){
    $email = NULL;
  }
  #Destinatario cpf cnpj
  $cnpj = $data[15];
  #Destinatario Telefone 1
  $telefone = $data[28];
  #Destinatario Complemento
  $complemento = utf8_encode($data[19]);
/*
$query = "INSERT INTO easytracking.tb_espo_entrega (track_expo, nome, local_entrega, numero, bairro, cidade, uf, cep, email, cnpj, telefone, complemento)
VALUES ('".$track_expo."', '".$destnome."', '".$local_entrega."', '".$numero."', '".$bairro."', '".$cidade."', '".$uf."', '".$cep."', '".$email."', '".$cnpj."',  
'".$telefone."', '".$complemento."')";

$result_coleta = mysql_query($query, $conecta) or print_r(mysql_error()); */


  /* Origem */
  #cnpj
  $cnpj = utf8_encode($data[1]);
  #serviço
  $tiposervico = utf8_encode($data[3]);
  #peso
  $peso = utf8_decode($data[5]);
  #volume
  $volume = utf8_decode($data[6]);
  #cod_frete
  $condfrete = utf8_decode($data[7]); 
  #numero_pedido
  $awb = utf8_decode($data[9]); 
  #nf
  $notafiscal = utf8_decode($data[38]);
  #u_empresa
  $empresa = utf8_decode($data[45]);
   #planilha NfeData
  $nfdata =  utf8_decode($data[40]);


$query1 = "INSERT INTO easytracking.tb_espo (track_expo,  awb, d_nome, cnpj, servico, peso_bruto, volume, cond_frete, nf, u_empresa, nfdata)
VALUES ('".$track_expo."', '".$awb."', '".$destnome."', '".$cnpj."','".$tiposervico."', '".$peso."', '".$volume."', '".$condfrete."', '".$notafiscal."', '".$empresa."', '".$nfdata."')";

$result_coleta1 = mysql_query($query1, $conecta) or print_r(mysql_error());


$query = "INSERT INTO easytracking.tb_espo_entrega (track_expo, nome, local_entrega, numero, bairro, cidade, uf, cep, email, cnpj, telefone, complemento)
VALUES ('".$track_expo."', '".$destnome."', '".$local_entrega."', '".$numero."', '".$bairro."', '".$cidade."', '".$uf."', '".$cep."', '".$email."', '".$cnpj."',  
'".$telefone."', '".$complemento."')";

$result_coleta = mysql_query($query, $conecta) or print_r(mysql_error());  


#print_r($query);
#die();


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
$conexao_ftp = ftp_connect($servidor_ftp, $port );

// Tenta fazer login
$login_ftp = ftp_login($conexao_ftp, $usuario_ftp, $senha_ftp );

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
  <strong>Arquivo enviado com sucesso!
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
