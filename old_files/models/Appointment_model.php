<?php

namespace App\Models;

class Appointment_model extends Crud_model
{
    protected $table = 'appointments';
    protected $allowedFields = ['name', 'email', 'date', 'time', 'phone', 'duration', 'message'];
    protected $useTimestamps = true;
     

      public function isSlotAvailable($date, $time)
    {
       $builder= $this->db->prefixTable('appointments');
       $sql='select * from 0 where date="'.$date.'" and time="'.$time.'"'; //die;
         return $this->db->query($sql);
      
    }

    public function getBookedSlots($date)
    {
       $builder= $this->db->prefixTable('appointments');
      echo $sql='select * from pcm_appointments where date="'.$date.'"'; 
       return $this->db->query($sql)->getResult();
        // return $builder->where('date', $date)->get()->getResult();
    }



    /**
     * send mail training
     * @param  [type] $email       
     * @param  [type] $sender_name 
     * @param  [type] $subject     
     * @param  [type] $body        
     * @return [type]              
     */
    public function send_mail_appointments($email,$sender_name,$subject,$body){
        
        $inbox = array();
        $inbox['to'] = $email;
        $inbox['sender_name'] = get_option('companyname');
        $inbox['subject'] = _strip_tags($subject);
        $inbox['body'] = _strip_tags($body);        
        $inbox['body'] = nl2br_save_html($inbox['body']);
        $inbox['date_received']      = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);
        
        if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 && strlen(get_option('smtp_username')) > 0){
            $ci = &get_instance();
            $ci->email->initialize();
            $ci->load->library('email');    
            $ci->email->clear(true);
            $ci->email->from(get_option('smtp_email'), $inbox['sender_name']);
            $ci->email->to($inbox['to']);
            
            $ci->email->subject($inbox['subject']);
            $ci->email->message($inbox['body']);

            $ci->email->send(true);
        }
        return true;
    }

}