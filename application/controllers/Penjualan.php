<?php
class Penjualan extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('url'));
		$this->load->model('roti_model');
		$this->load->model('Penjualan_model');
	}

	function index(){

		$data['data']=$this->roti_model->tampil_roti();
		$title=array(
	        'title'=>'Penjualan'
	    );	    	    
	    $kode['kode'] = $this->Penjualan_model->get_notrans();
		$this->load->view('element/header', $title);
		$this->load->view('v_penjualan',$data+$kode);
		
		// $this->load->view('element/footer');
		 // variable $kodeunik merujuk ke file model_user.php pada function buat_kode. paham kan ya? harus paham dong
       
	}

	public function get_autocomplete(){    //membuat dropdown pilihan di search box
        if (isset($_GET['term'])) {
            $result = $this->Penjualan_model->search($_GET['term']);
            if (count($result) > 0) {
            foreach ($result as $row)
                $arr_result[] = array(
                	'label'=> $row->nama_roti,
                	'id_roti' => $row->id_roti
                );
                echo json_encode($arr_result);
            }
        }
    }
	
	function insert(){
		$id_roti = $this->input->post('id_roti');
		$nama_roti = $this->input->post('nama_roti');
		$jumlah = $this->input->post('jumlah');

		//gk ro teko endi no transaksine
		//njupuk harga e , trus ngitung harga x jumlah ndek endi yo gk paham T.T
		$data = array(
			'id_roti' => $id_roti,
			'nama_roti' => $nama_roti,
			'jumlah' => $jumlah
			);
		$this->Penjualan_model->insert($data,'tabel_detail_sip');
	} 

	function get_roti(){
		$id_roti=$this->input->post('id_roti');
		$x['roti']=$this->roti_model->get_roti($id_roti);
		$this->load->view('v_detail_jual',$x);

	}

	function add_to_cart(){
		$id_roti=$this->input->post('id_roti');
		$produk=$this->roti_model->get_roti($id_roti);
		$i=$produk->row_array();
		$data = array(
               'id'       => $i['id_roti'],
               'name'     => $i['nama_roti'],
              // 'stok'     => $i['stok'],
               'qty'      => $this->input->post('qty'),
               'amount'	  => str_replace(",", "", $this->input->post('harga'))
            );

		if(!empty($this->cart->total_items())){
			foreach ($this->cart->contents() as $items){
				$id=$items['id'];
				$qtylama=$items['qty'];
				$rowid=$items['rowid'];
				$id_roti=$this->input->post('id_roti');
				$qty=$this->input->post('qty');
				if($id==$id_roti){
					$up=array(
						'rowid'=> $rowid,
						'qty'=>$qtylama+$qty
						);
					$this->cart->update($up);
				}else{
					$this->cart->insert($data);
				}
			}
		}else{
			$this->cart->insert($data);
		}

			redirect('Penjualan');

		}

	function remove(){
		$row_id=$this->uri->segment(4);
		$this->cart->update(array(
               'rowid'      => $row_id,
               'qty'     => 0
            ));
		redirect('Penjualan');

	}

	function simpan_penjualan(){
		$total=$this->input->post('total');
		$jml_uang=str_replace(",", "", $this->input->post('jml_uang'));
		$kembalian=$jml_uang-$total;
		if(!empty($total) && !empty($jml_uang)){
			if($jml_uang < $total){
				echo $this->session->set_flashdata('msg','<label class="label label-danger">Jumlah Uang yang anda masukan Kurang</label>');
				redirect('Penjualan');
			}else{
				$notrans=$this->Penjualan_model->get_notrans();
				$this->session->set_userdata('notrans',$notrans);
				$order_proses=$this->Penjualan_model->simpan_penjualan($notrans,$total_jual,$uang,$kembalian);
				if($order_proses){
					$this->cart->destroy();
				}else{
					redirect('Penjualan');
				}
			}
			
		}else{
			echo $this->session->set_flashdata('msg','<label class="label label-danger">Penjualan Gagal di Simpan, Mohon Periksa Kembali Semua Inputan Anda!</label>');
			redirect('Penjualan');
		}

	}

	function cetak_faktur(){
		$x['data']=$this->m_penjualan->cetak_faktur();
		$this->load->view('admin/laporan/v_faktur',$x);
		//$this->session->unset_userdata('nofak');
	}


}
?>
