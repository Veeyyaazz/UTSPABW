<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hotel_model extends CI_Model
{
    public function import_data($datahotel)
    {
        $jumlah = count($datahotel);
        if ($jumlah > 0) {
            $this->db->replace('m_hotel', $datahotel);
        }
    }

    public function getDataHotel($tanggalawal = null, $tanggalakhir = null)
    {
        $tanggalawalbaru = strtotime($tanggalawal);
        $tanggalakhirbaru = strtotime($tanggalakhir);
        if ($tanggalawal && $tanggalakhir) {
            $this->db->where('date_created >=', $tanggalawalbaru);
            $this->db->where('date_created <=', $tanggalakhirbaru);
        }
        return $this->db->get('m_hotel')->result_array();
    }
    public function getHotelById($id)
    {
        return $this->db->get_where('hotel', ['id' => $id])->row_array();
    }
    public function hapusDataHotel($id)
    {
        // $this->db->where('id', $id);
        $this->db->delete('m_hotel',['id'=>$id]);    
    }

}