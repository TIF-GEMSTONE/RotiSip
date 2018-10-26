<?php
class Produk_model extends CI_Model {
  public function __construct() {
        parent::__construct();
  }

  public function input($data){
    try{
      $this->db->insert('tabel_roti', $data);
      return true;
    }catch(Exception $e){
    }
  }

  public function data(){
   $this->db->select('*');
   $this->db->from('tabel_roti');
   $data = $this->db->get();
   return $data->result();
 }

 public function getid($id){
     $this->db->where('id', $id);
     return $this->db->get('tabel_roti')->result();
   }

 public function gambar($id)
   {
     $this->db->where('id', $id);
     return $this->db->get('tabel_roti')->row();
   }

function ubah($data, $id){
    $this->db->where('id_roti',$id);
    $this->db->update('tabel_roti', $data);
    return TRUE;
}

 public function hapus($id){
   $this->db->where('id', $id);
   $this->db->delete('tabel_roti');
 }



}
?>
