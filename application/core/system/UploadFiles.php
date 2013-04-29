<?php

class UploadFiles{

	/**
	 * Guarda la imagen de un empleado
	 */
	public static function uploadEmpresaLogo(){
		$ci =& get_instance();
		if(isset($_FILES['dlogo'])){
			if($_FILES['dlogo']['name']!=''){
				$config['upload_path'] = APPPATH.'images/empresas/';
				$config['allowed_types'] = 'jpg|jpeg|gif|png';
				$config['max_size']	= '200';
				$config['max_width'] = '1024';
				$config['max_height'] = '768';
				$config['encrypt_name'] = true;
				$ci->load->library('upload', $config);
				if(!$ci->upload->do_upload('dlogo')){
					$data = array(false, $ci->upload->display_errors());
				}else{
					$data = array(true, $ci->upload->data());
					$config = array();
					$config['image_library'] = 'gd2';
					$config['source_image']	= $data[1]['full_path'];
					$config['create_thumb'] = false;
					$config['master_dim'] = 'auto';
					$config['width']	 = 200;
					$config['height']	= 200;

					$ci->load->library('image_lib', $config);
					$ci->image_lib->resize();
				}
				return $data;
			}
			return false;
		}

		return 'ok';
	}


	public static function deleteFile($path){
		$path = str_replace(base_url(), '', $path);
		try {
			if(file_exists($path))
				unlink($path);
			return true;
		}catch (Exception $e){}
		return false;
	}



	/**
	 * Guarda la imagen de una serie y folio
	 */
	public static function uploadImgFamilia(){
		$ci =& get_instance();
		if(isset($_FILES['dimagen'])){
			if($_FILES['dimagen']['name']!=''){
				$config['upload_path']   = APPPATH.'images/familias/';
				$config['allowed_types'] = 'jpg|jpeg|gif|png';
				$config['max_size']      = '200';
				$config['max_width']     = '1024';
				$config['max_height']    = '1024';
				$config['encrypt_name']  = true;
				$ci->load->library('upload', $config);
				if(!$ci->upload->do_upload('dimagen')){
					$data = array(false, $ci->upload->display_errors());
				}else{
					$data                    = array(true, $ci->upload->data());
					$config                  = array();
					$config['image_library'] = 'gd2';
					$config['source_image']  = $data[1]['full_path'];
					$config['create_thumb']  = false;
					if ($data[1]['image_width'] >= $data[1]['image_height'])
						$config['master_dim']  = 'height';
					else
						$config['master_dim']  = 'width';
					$config['height']        = 150;
					$config['width']         = 150;

					$ci->load->library('image_lib', $config);
					$ci->image_lib->resize();

					$config                   = array();
					$config['image_library']  = 'gd2';
					$config['source_image']   = $data[1]['full_path'];
					$config['x_axis']         = 0;
					$config['y_axis']         = 0;
					$config['height']         = 150;
					$config['width']          = 150;
					$config['maintain_ratio'] = false;
					$ci->image_lib->initialize($config); 
					$ci->image_lib->crop();
				}
				return $data;
			}
			return false;
		}

		return 'ok';
	}

}
?>