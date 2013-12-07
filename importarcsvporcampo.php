<?php
/**********
 * v 2.3 Quita logotipo
 * v 1.3: Añadide referencia interna como campo modificable
 * v 1.4: Añade peso como campo modificable
 * v 1.4.1: Corregido bug: no importa valores no numéricos
 * v 1.5.0: Añade campo "activo" como campo modificable
 * v 1.5.1:     No importa precios no numericos
 *              Actualiza campo "on_sale" (en rebajas)
 * v 2.0.0: Compatible con PS 1.5
 * v 2.0.1: Variable $sql2 no inicalizada
 * v 2.1.0: Añade subida de fichero
 */
class ImportarCsvPorCampo extends Module
{

        public $error = 0;
        function __construct()
        {
                $this->name = 'importarcsvporcampo';
                $this->tab = 'http://todoprestashop.com | [hans] | Adaptado por Proa It Services';


                parent::__construct();

                $this->displayName = $this->l('ImportarCsvPorCampo');
                $this->description = $this->l('Actualizar tu tabla de productos importando por un campo');
                $this->version = '2.0.1';

                $campo_buscar = strval(Configuration::get('IMPORTARCSVPORCAMPO_buscar'));
                $caracter_separador = strval(Configuration::get('IMPORTARCSVPORCAMPO_separador'));
                $campo_actualizar = strval(Configuration::get('IMPORTARCSVPORCAMPO_actualizar'));
                $posicion_csv = strval(Configuration::get('IMPORTARCSVPORCAMPO_posicion'));
                $posicion2_csv = strval(Configuration::get('IMPORTARCSVPORCAMPO_posicion2'));


                if (!isset($campo_buscar) OR $campo_buscar=="")
                                                                $error_i[] = $this->l('el Campo a Buscar en la tabla de de Productos');
                if (!isset($caracter_separador) OR $caracter_separador=="")
                                                                $error_i[] = $this->l('el Caracter Separador del fichero CSV');
                if (!isset($campo_actualizar) OR $campo_actualizar=="")
                                                                $error_i[] = $this->l('el Campo a Actualizar en tu tabla de Productos');
                if (!isset($posicion_csv) OR $posicion_csv=="")
                                                                $error_i[] = $this->l('la posición del campo dentro del fichero CSV');
                if (!isset($posicion2_csv) OR $posicion2_csv=="")
                                                                $error_i[] = $this->l('la posición2 del campo dentro del fichero CSV');


                if (isset($error_i) AND sizeof($error_i)>0) {
                                $mierror=$this->l('No se ha definido --> ');
                                $i=0;
                                foreach ($error_i AS $err) {
                                                if ($i>0) $txt=", ";
                                                 else $txt="";
                                                $mierror .= $txt.$err;
                                                $i++;
                                }
                                $mierror=$mierror . " ". $this->l('para el módulo de Importación');
                                $this->warning=$mierror;
                }


        }

        function install()
        {
                        Configuration::updateValue('IMPORTARCSVPORCAMPO_buscar', "");
                        Configuration::updateValue('IMPORTARCSVPORCAMPO_separador', "");
                        Configuration::updateValue('IMPORTARCSVPORCAMPO_actualizar', "");
                        Configuration::updateValue('IMPORTARCSVPORCAMPO_posicion', "");
                        Configuration::updateValue('IMPORTARCSVPORCAMPO_posicion2', "");
                        if (parent::install() == false)
                                        return false;
                        return true;
        }

        public function uninstall()
        {
                        Configuration::deleteByName('IMPORTARCSVPORCAMPO_buscar');
                        Configuration::deleteByName('IMPORTARCSVPORCAMPO_separador');
                        Configuration::deleteByName('IMPORTARCSVPORCAMPO_actualizar');
                        Configuration::deleteByName('IMPORTARCSVPORCAMPO_posicion');
                        Configuration::deleteByName('IMPORTARCSVPORCAMPO_posicion2');
                        return parent::uninstall();
        }



        public function tiene_variedades($id_product) {

                        $sql ='         SELECT `id_product`
                                                        FROM `'._DB_PREFIX_.'product_attribute`
                                                        WHERE `id_product` = '.intval($id_product);
                        //echo $sql;
                        $sql = Db::getInstance()->getRow($sql);
                        if (empty($result) === true OR  $result === false OR !sizeof($result))  return false;
                                        else return true;

        }

        public function buscar_producto($quebuscar, $campo) {
                        $sql = '        SELECT '.$quebuscar.'
                                                        FROM `'._DB_PREFIX_.'product`
                                                        WHERE '.$quebuscar.' = '.trim($campo);
                        //echo $sql;
                        $sql = Db::getInstance()->Executes($sql);
                        $resultados = sizeof($sql);
                        if ($resultados>0) return true;
                                        else return false;
        }

        public function actualizar_producto($quebuscar, $campo, $campo_actualizar, $valor) {
                $sql= null;
                $sql2= null;
                $resultados = 1;
                $resultados2 = 1;
                // el precio debe ser numérico
                if ($campo_actualizar=="price" && !is_numeric($valor))
                {
                                return false;
                }
                if(version_compare(_PS_VERSION_, '1.5') >= 0){
                        // Version 1.5
                        switch ($campo_actualizar) {
                                case "price":
                                        $sql = "update `"._DB_PREFIX_."product`
                                                SET ".$campo_actualizar."='".$valor."'
                                                WHERE ".$quebuscar." = '".trim($campo)."'";
                                        $sql2 = "update `"._DB_PREFIX_."product_shop`
                                                SET ".$campo_actualizar."='".$valor."'
                                                WHERE id_product = (SELECT id_product FROM "._DB_PREFIX_."product WHERE ".$quebuscar." = ".intval($campo) . ")";
                                        break;
                                case "quantity":
                                        $sql = "update `"._DB_PREFIX_."product`
                                                SET ".$campo_actualizar."='".$valor."'
                                                WHERE ".$quebuscar." = '".trim($campo)."'";

                                        $sql2 = "update `"._DB_PREFIX_."stock_available`
                                                SET ".$campo_actualizar."=".$valor."
                                                WHERE id_product = (SELECT id_product FROM "._DB_PREFIX_."product WHERE ".$quebuscar." = ".intval($campo) . ")";
                                        break;
                                case "reference":
                                        $sql = "update `"._DB_PREFIX_."product`
                                                SET ".$campo_actualizar."='".$valor."'
                                                WHERE ".$quebuscar." = '".trim($campo)."'";
                                        break;
                                case "weight":
                                        $sql = "update `"._DB_PREFIX_."product`
                                                SET ".$campo_actualizar."='".$valor."'
                                                WHERE ".$quebuscar." = '".trim($campo)."'";
                                        break;
                                case "active":
                                        $sql = "update `"._DB_PREFIX_."product`
                                                SET ".$campo_actualizar."='".$valor."'
                                                WHERE ".$quebuscar." = '".trim($campo)."'";
                                        $sql2 = "update `"._DB_PREFIX_."product_shop`
                                                SET ".$campo_actualizar."='".$valor."'
                                                WHERE id_product = (SELECT id_product FROM "._DB_PREFIX_."product WHERE ".$quebuscar." = ".intval($campo) . ")";
                                        break;
                                case "on_sale":
                                        $sql = "update `"._DB_PREFIX_."product`
                                                SET ".$campo_actualizar."='".$valor."'
                                                WHERE ".$quebuscar." = '".trim($campo)."'";
                                        $sql2 = "update `"._DB_PREFIX_."product_shop`
                                                SET ".$campo_actualizar."='".$valor."'
                                                WHERE id_product = (SELECT id_product FROM "._DB_PREFIX_."product WHERE ".$quebuscar." = ".intval($campo) . ")";
                                        break;
                                default:
                                        // No implementado en 1.5
                                        return false;
                        }
                }
                else {
                        // Versiom 1.4/1.3

                        $sql = "update `"._DB_PREFIX_."product`
                                        SET ".$campo_actualizar."='".$valor."'
                                        WHERE ".$quebuscar." = '".trim($campo)."'";
                }
                $sql = Db::getInstance()->Execute($sql);
                $resultados = sizeof($sql);
                if ( !is_null($sql2) ) {
//                        echo "DEBUG sql2=[$sql2]";
                        $sql2 = Db::getInstance()->Execute($sql2);
                        $resultados2 = sizeof($sql2);
                }
//              echo "DEBUG#Modificados[$resultados][$resultados2]";
                if ($resultados>0 && $resultados2>0) return true;
                                else return false;
        }



        public function cargarCSV(){

                        $error = 0;
                        $campo_buscar = strval(Configuration::get('IMPORTARCSVPORCAMPO_buscar'));
                        $caracter_separador = strval(Configuration::get('IMPORTARCSVPORCAMPO_separador'));
                        $campo_actualizar = strval(Configuration::get('IMPORTARCSVPORCAMPO_actualizar'));
                        $posicion_csv = strval(Configuration::get('IMPORTARCSVPORCAMPO_posicion'));
                        $posicion2_csv = strval(Configuration::get('IMPORTARCSVPORCAMPO_posicion2'));

                        $fp = fopen ( dirname(__FILE__)."/update.csv" , "r" );

                        while (( $data = fgetcsv ( $fp , 1000 , $caracter_separador )) !== FALSE ) {
                                $campos=count($data);

                                // hay errores?
                                if ($posicion_csv > $campos || $campos==0) {
                                        $error=1;
                                        break;
                                }

                                        $valor=$data[$posicion2_csv-1];
                                        $campo=$data[$posicion_csv-1];
                                        // Proa Mod
                                        //if ($campo>0 && $valor>0) {
                                        if ($campo<>"" && $valor<>"") {
                                                          if ($this->buscar_producto($campo_buscar, $campo))  {
                                                                        if (!$this->tiene_variedades($campo)) {
                                                                           // es un producto sin variedades, y existe --> hay que actualizarlo
                                                                           if ($this->actualizar_producto($campo_buscar, $campo, $campo_actualizar, $valor))
                                                                                        echo "Actualizado ".$campo_buscar. " =".$campo." con el valor: ".$valor."<br>";
                                                                        }
                                                                        else
                                                                                        echo "No se actualizará: ".$campo. " porque tiene variedades<br/>";
                                                         }
                                        }

                        }

                        if ($error==1) echo "No existe esa columna: ".$posicion_csv." en el fichero csv";
                        fclose ( $fp );
        }

        public function getContent()
        {
                        $output = '<h2>'.$this->displayName.'</h2>';
                        if (Tools::isSubmit('saveCONFIG') || Tools::isSubmit('submitCSV'))
                        {
                                        // GUARDAR CONFIGURACION

                                        $buscar = strval(Tools::getValue('buscar'));
                                        $separador = strval(Tools::getValue('separador'));
                                        $actualizar = strval(Tools::getValue('actualizar'));
                                        $posicion = strval(Tools::getValue('posicion'));
                                        $posicion2 = strval(Tools::getValue('posicion2'));

                                        if (!$buscar OR $buscar == "")
                                                        $errors[] = $this->l('Campo a Buscar Incorrecto');
                                        else
                                                        Configuration::updateValue('IMPORTARCSVPORCAMPO_buscar', $buscar);

                                        if (!$separador OR $separador == "")
                                                        $errors[] = $this->l('Campo Separador Incorrecto');
                                        else
                                                        Configuration::updateValue('IMPORTARCSVPORCAMPO_separador', $separador);


                                        if (!$actualizar OR $actualizar == "")
                                                        $errors[] = $this->l('Campo a Actualizar Incorrecto');
                                        else
                                                        Configuration::updateValue('IMPORTARCSVPORCAMPO_actualizar', $actualizar);


                                        if (!$posicion  OR $posicion == "" OR !Validate::isInt($posicion))
                                                        $errors[] = $this->l('Posición Incorrecta dentro del fichero CSV');
                                        else
                                                        Configuration::updateValue('IMPORTARCSVPORCAMPO_posicion', $posicion);


                                        if (!$posicion2  OR $posicion2 == "" OR !Validate::isInt($posicion2))
                                                        $errors[] = $this->l('Posición2 Incorrecta dentro del fichero CSV');
                                        else
                                                        Configuration::updateValue('IMPORTARCSVPORCAMPO_posicion2', $posicion2);


                                        if (isset($errors) AND sizeof($errors)) {
                                                        $output .= $this->displayError(implode('<br />', $errors));
                                                        $error = 1;
                                        }
                                        else if  (Tools::isSubmit('submitCSV')){

                                                        $this->cargarCSV();
                                        }
                        }
						if (Tools::isSubmit('submitUPLOAD')){
							$resultadoUpload=$this->subeArchivo();
							if ($resultadoUpload!="") {
								$errors[] = $resultadoUpload;
								$output .= $this->displayError(implode('<br />', $errors));
                                $error = 1;
								
							}
						}

                        return $output.$this->displayForm();

        }

        public function displayForm()
        {
                $error=0;
                $x0 = $x1 = $x2 = $x3 = $x4 = $x5 = $x6 = $x7 = $x8 = $x9 = $x10 = null;
                $u0 = $u1 = $u2 = $u3 = $u4 = $u5 = $u6 = null;
                $v0 = $v1 = $v2 = $v3 = $v4 = $v5 = $v6 = $v7 = $v8 = $v9 = $v10 = null;
                $t0 = $t1 = $t2 = $t3 = null;
                $s0 = $s1 = $s2 = $s3 = null;

                        $b=Tools::getValue('buscar', Configuration::get('IMPORTARCSVPORCAMPO_buscar'));
                        if ($b!="") {
                                        switch ($b) {
                                          case "reference":             $s1=' selected="selected"';break;
                                          case "supplier_reference":    $s2=' selected="selected"';break;
                                          case "ean13":                 $s3=' selected="selected"';break;
                                          default:                      $s0=' selected="selected"';break;
                                        }
                        }
                        else
                                        $s0=' selected="selected"';

                        $s=Tools::getValue('separador', Configuration::get('IMPORTARCSVPORCAMPO_separador'));
                        if ($s!="") {
                                        switch ($s) {
                                          case ";": $t1=' selected="selected"';break;
                                          case ":": $t2=' selected="selected"';break;
                                          case "|": $t3=' selected="selected"';break;
                                          default:  $t0=' selected="selected"';break;
                                        }
                        }
                        else
                                        $t0=' selected="selected"';

                        $a=Tools::getValue('actualizar', Configuration::get('IMPORTARCSVPORCAMPO_actualizar'));
                        if ($a!="") {
                                        switch ($a) {
                                                        case "price":   $u1=' selected="selected"';break;
                                                        case "quantity":        $u2=' selected="selected"';break;
                                                        case "reference": $u3=' selected="selected"';break;
                                                        case "weight":  $u4=' selected="selected"';break;
                                                        case "active": $u5='  selected="selected"';break;
                                                        case "on_sale": $u6='  selected="selected"';break;
                                                        default:                $u0=' selected="selected"';break;
                                        }
                        }
                        else
                                        $u0=' selected="selected"';

                        $p=Tools::getValue('posicion', Configuration::get('IMPORTARCSVPORCAMPO_posicion'));
                        if ($p!="") {
                                        switch ($p) {
                                          case "1": $v1=' selected="selected"';break;
                                          case "2": $v2=' selected="selected"';break;
                                          case "3": $v3=' selected="selected"';break;
                                          case "4": $v4=' selected="selected"';break;
                                          case "5": $v5=' selected="selected"';break;
                                          case "6": $v6=' selected="selected"';break;
                                          case "7": $v7=' selected="selected"';break;
                                          case "8": $v8=' selected="selected"';break;
                                          case "9": $v9=' selected="selected"';break;
                                          case "10": $v10=' selected="selected"';break;
                                          default:  $v0=' selected="selected"';break;
                                        }
                        }
                        else
                                        $v0=' selected="selected"';

                        $x=Tools::getValue('posicion2', Configuration::get('IMPORTARCSVPORCAMPO_posicion2'));
                        if ($x!="") {
                                        switch ($x) {
                                          case "1": $x1=' selected="selected"';break;
                                          case "2": $x2=' selected="selected"';break;
                                          case "3": $x3=' selected="selected"';break;
                                          case "4": $x4=' selected="selected"';break;
                                          case "5": $x5=' selected="selected"';break;
                                          case "6": $x6=' selected="selected"';break;
                                          case "7": $x7=' selected="selected"';break;
                                          case "8": $x8=' selected="selected"';break;
                                          case "9": $x9=' selected="selected"';break;
                                          case "10": $x10=' selected="selected"';break;
                                          default:  $x0=' selected="selected"';break;
                                        }
                        }
                        else
                                        $x0=' selected="selected"';



                        if (isset($v0) || isset($u0) || isset($t0) || isset($s0) || isset($x0)) $error=1;

                        $output = '
                        <div style="text-align:center">
                                        <a href="http://www.proaitservices.com" target="_blank">
                                                        Proa IT Services
                                        </a>
                        </div>

                        <form action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data" method="post">
                                        <fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Configura Módulo').'</legend>


                                                        <textarea style="width:90%;height:40px;margin:10px 40px">'.
                                                        $this->l('Antes de realizar ninguna importación haz backup de tu base de datos, o al menos de tus tablas de Productos. ').
                                                        $this->l('NO nos hacemos responsables de su mal funcionamiento')
                                                        .'</textarea>
                                                        <div style="clear:both">&nbsp;</div>


                                                        <label>'.$this->l('Campo a buscar en la tabla de Productos').'</label>
                                                        <div class="margin-form">
                                                                        <select name="buscar">
                                                                                        <option value="" '.$s0.' />-- Seleccione campo a buscar --</option>
                                                                                        <option value="reference" '.$s1.' />'.$this->l('Referencia ').'</option>
                                                                                        <option value="supplier_reference" '.$s2.' />'.$this->l('Referencia del Proveedor').'</option>
                                                                                        <option value="ean13" '.$s3.' />'.$this->l('EAN13 del Producto').'</option>
                                                                        </select>
                                                        </div>

                                                        <div style="clear:both">&nbsp;</div>

                                                        <label>'.$this->l('¿Qué posición en el fichero csv ocupa ese campo?').'</label>
                                                        <div class="margin-form">
                                                                        <select name="posicion">
                                                                                        <option value="" '.$v0.' />-- Seleccione la posición --</option>
                                                                                        <option value="1" '.$v1.' />'.$this->l('1').'</option>
                                                                                        <option value="2" '.$v2.' />'.$this->l('2').'</option>
                                                                                        <option value="3" '.$v3.' />'.$this->l('3').'</option>
                                                                                        <option value="4" '.$v4.' />'.$this->l('4').'</option>
                                                                                        <option value="5" '.$v5.' />'.$this->l('5').'</option>
                                                                                        <option value="6" '.$v6.' />'.$this->l('6').'</option>
                                                                                        <option value="7" '.$v7.' />'.$this->l('7').'</option>
                                                                                        <option value="8" '.$v8.' />'.$this->l('8').'</option>
                                                                                        <option value="9" '.$v9.' />'.$this->l('9').'</option>
                                                                                        <option value="10" '.$v10.' />'.$this->l('10').'</option>

                                                                        </select>
                                                        </div>



                                                        <div style="clear:both">&nbsp;</div>

                                                        <label>'.$this->l('Carácter Separador del Fichero CSV').'</label>
                                                        <div class="margin-form">
                                                                        <select name="separador">
                                                                                        <option value="" '.$t0.' />-- Seleccione Caracter Separador --</option>
                                                                                        <option value=";" '.$t1.' />&nbsp;'.$this->l(';').'&nbsp;</option>
                                                                                        <option value=":" '.$t2.' />&nbsp;'.$this->l(':').'&nbsp;</option>
                                                                                        <option value="|" '.$t3.' />&nbsp;'.$this->l('|').'&nbsp;</option>
                                                                        </select>
                                                        </div>

                                                        <div style="clear:both">&nbsp;</div>

                                                        <label>'.$this->l('¿Qué campo de la tabla de productos quieres actualizar?').'</label>
                                                        <div class="margin-form">
                                                                        <select name="actualizar">
                                                                                        <option value="" '.$u0.' />-- Seleccione campo a actualizar --</option>
                                                                                        <option value="price" '.$u1.' />&nbsp;'.$this->l('Precio').'&nbsp;</option>
                                                                                        <option value="quantity" '.$u2.' />&nbsp;'.$this->l('Stock').'&nbsp;</option>
                                                                                        <option value="reference" '.$u3.' />&nbsp;'.$this->l('Referencia').'&nbsp;</option>
                                                                                        <option value="weight" '.$u4.' />&nbsp;'.$this->l('Peso').'&nbsp;</option>
                                                                                        <option value="active" '.$u5.' />&nbsp;'.$this->l('Activo').'&nbsp;</option>
                                                                                        <option value="on_sale" '.$u6.' />&nbsp;'.$this->l('En rebajas').'&nbsp;</option>
                                                                        </select>
                                                        </div>

                                                        <div style="clear:both">&nbsp;</div>

                                                        <label>'.$this->l('¿Qué posición en el fichero csv ocupa ese campo?').'</label>
                                                        <div class="margin-form">
                                                                        <select name="posicion2">
                                                                                        <option value="" '.$x0.' />-- Seleccione la posición --</option>
                                                                                        <option value="1" '.$x1.' />'.$this->l('1').'</option>
                                                                                        <option value="2" '.$x2.' />'.$this->l('2').'</option>
                                                                                        <option value="3" '.$x3.' />'.$this->l('3').'</option>
                                                                                        <option value="4" '.$x4.' />'.$this->l('4').'</option>
                                                                                        <option value="5" '.$x5.' />'.$this->l('5').'</option>
                                                                                        <option value="6" '.$x6.' />'.$this->l('6').'</option>
                                                                                        <option value="7" '.$x7.' />'.$this->l('7').'</option>
                                                                                        <option value="8" '.$x8.' />'.$this->l('8').'</option>
                                                                                        <option value="9" '.$x9.' />'.$this->l('9').'</option>
                                                                                        <option value="10" '.$x10.' />'.$this->l('10').'</option>

                                                                        </select>
                                                        </div>

                                                        <div style="clear:both">&nbsp;</div>
                                                        <label>'.$this->l('Fichero update.csv').'</label>
                                                        ';

                                                        $fp = @fopen (dirname(__FILE__)."/update.csv" , "r");
                                                        if ($fp) {
                                                                        $output .= '<div class="margin-form"><input type="text" name="info" style="width:250px;background-color:#0f0;" disabled="disabled" value="'.$this->l('Fichero update.csv disponible').'" /></div>';
                                                                        fclose ($fp);
                                                        }
                                                        else {
                                                                        $error=1;
                                                                        $output .= '<div class="margin-form"><input type="text" name="info"  style="width:250px;background-color:#f00" disabled="disabled" value="'.$this->l('NO EXISTE el Fichero update.csv').'" /></div>';
                                                        }
                                                        $output .= '
                                                        <center>
                                                                        <input type="submit" name="saveCONFIG" value="'.$this->l('Guardar Configuracion').'" class="button"  />
                                                        ';

                                                        if ($error!=1) $output .= '<input type="submit" name="submitCSV" value="'.$this->l('Actualizar Tabla').'" class="button" />';

                                                        $output .= '
                                                        </center>
                                        </fieldset>
                        </form>
                        <fieldset><legend>'.$this->l('Sube archivo').'</legend>
                        <label  for="userfile">'.$this->l('Subir fichero').'</label>
                                                        <div class="margin-form">
															<form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">
															<fieldset>
																<input type="hidden" name="MAX_FILE_SIZE" value="102400000" />
																<input name="userfile" type="file" id="userfile" />
																<input type="submit" name="submitUPLOAD" value="subir" />
															</fieldset>
															</form> 
                                                        </div>
                                                        
                                                        <div style="clear:both">&nbsp;</div>
                         </fieldset>
                        ';
                        return $output;
        }
        
        /** Procesa la subuida de un archivo
         * y lo deja como update.csv en el directorio del módulo
         **/
        public function subeArchivo()
        {
			// En versiones de PHP anteriores a 4.1.0, $HTTP_POST_FILES debe utilizarse en lugar
			// de $_FILES.
			$output = "";
			//$uploaddir = '/explo/canalpyme/clientes/100000/aa100000/site_proa/htdocs/ip/upload/';
			//$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
			
			$uploadfile = dirname(__FILE__)."/update.csv";
			if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
				$output="El archivo fue cargado exitosamente.";
			} else {
				$output="Error al cargar el archivo";
			}

			/**
			echo 'DEBUG[';
			echo $uploadfile;
			print_r($_FILES);
			echo ']';
			**/
			return $output;
		}

}

?>

