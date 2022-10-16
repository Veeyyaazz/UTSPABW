<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Hotel extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Hotel_model');
    }



    public function index()
    {
        $tanggalawal = $this->input->get('tanggalawal');
        $tanggalakhir = $this->input->get('tanggalakhir');
        $data['title'] = 'Export Import';
        $data['semuahotel'] = $this->Hotel_model->getDataHotel($tanggalawal, $tanggalakhir);
        $this->load->view('index/index', $data);
    }

    public function uploaddata()
    {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['file_name'] = 'doc' . time();
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('importexcel')) {
            $file = $this->upload->data();
            $reader = ReaderEntityFactory::createXLSXReader();

            $reader->open('uploads/' . $file['file_name']);
            foreach ($reader->getSheetIterator() as $sheet) {
                $numRow = 1;
                foreach ($sheet->getRowIterator() as $row) {
                    if ($numRow > 1) {
                        $datahotel = array(
                            'kode_hotel'  => $row->getCellAtIndex(1),
                            'nama'  => $row->getCellAtIndex(2),
                            'alamat'       => $row->getCellAtIndex(3),
                            'rate_hotel'       => $row->getCellAtIndex(4),
                            'date_created' => time(),
                            'date_modified' => time(),
                        );
                        $this->Hotel_model->import_data($datahotel);
                    }
                    $numRow++;
                }
                $reader->close();
                unlink('uploads/' . $file['file_name']);
                $this->session->set_flashdata('pesan', 'import Data Berhasil');
                redirect('hotel');
            }
        } else {
            echo "Error :" . $this->upload->display_errors();
        };
    }
    public function mpdf()
    {
        $tanggalawal = $this->input->get('tanggalawal');
        $tanggalakhir = $this->input->get('tanggalakhir');
        $mpdf = new \Mpdf\Mpdf();
        $datahotel = $this->Hotel_model->getDataHotel($tanggalawal, $tanggalakhir);
        $data = $this->load->view('pdf/mpdf', ['semuahotel' => $datahotel], TRUE);
        $mpdf->WriteHTML($data);
        $mpdf->Output();
    }

    public function excel()
    {
        $tanggalawal = $this->input->get('tanggalawal');
        $tanggalakhir = $this->input->get('tanggalakhir');
        $data['title'] = 'Laporan Excel';
        $data['semuahotel'] = $this->Hotel_model->getDataHotel($tanggalawal, $tanggalakhir);
        $this->load->view('excel/excel', $data);
    }

    public function highchart()
    {
        $tanggalawal = $this->input->get('tanggalawal');
        $tanggalakhir = $this->input->get('tanggalakhir');
        $data['title'] = 'Export Grafik';
        $data['semuahotel'] = $this->Hotel_model->getDataHotel($tanggalawal, $tanggalakhir);
        $this->load->view('grafik/highchart', $data);
    }

    public function hapus($id)
    {
        $this->Hotel_model->hapusdatahotel($id);
        $this->session->set_flashdata('flash', 'Dihapus');
        redirect('hotel');
    }
}
