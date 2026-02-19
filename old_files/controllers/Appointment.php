<?php

namespace App\Controllers;

use App\Libraries\ReCAPTCHA;
use CodeIgniter\Database\Config;
use CodeIgniter\Controller;

class Appointment extends App_Controller
{
      function __construct() {
         $this->Appointment_model = model('Appointment_model');
         $this->Email_templates_model = model('Email_templates_model');
    }


    public function form()
    {
        return view('AppointmentScheduler/form');
    }

    public function submit()
    {
        helper(['form']);
        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => 'required',
            'email' => 'required|valid_email',
            'date' => 'required',
            'time' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        print_r($this->request->getPost()); die;
        $response=$this->Appointment_model->save([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'appointment_date' => $this->request->getPost('date'),
            'appointment_time' => $this->request->getPost('time'),
            'duration_minutes' => $this->request->getPost('duration'),
            'remarks' => $this->request->getPost('message'),
            'phone' => $this->request->getPost('phone'),
        ]);
        $name=$this->request->getPost('name');
        $email=$this->request->getPost('email');
        $date=$this->request->getPost('date');
        $time=$this->request->getPost('time');
        $duration=$this->request->getPost('duration');
        $message=$this->request->getPost('message');
        $phone=$this->request->getPost('phone');
        if($response){            
            $email_to=$this->request->getPost('email');
            $subject='New Appointment Added';
            $email_details='
            <table> 
            <tr> <td>Name:</td><td>'.$name.'</td> </tr>
            <tr> <td>Email:</td><td>'.$email.'</td> </tr>
            <tr> <td>Phone:</td><td>'.$phone.'</td> </tr>
            <tr> <td>Date:</td><td>'.$date.'</td> </tr>
            <tr> <td>Time:</td><td>'.$time.'</td> </tr>
            <tr> <td>Duration:</td><td>'.$duration.'</td> </tr>
            <tr> <td>Message:</td><td>'.$message.'</td> </tr>
            </table>

            ';
            $email_template = $this->Email_templates_model->get_final_template("general_notification");

                $message=$email_template->message;
                $message=str_replace('{EVENT_TITLE}',$subject,$message);
                $message=str_replace('{EVENT_DETAILS}',$email_details,$message);
                send_app_mail($email_to, $email_template->subject, $message);
        }


        // You can send email/notifications here if needed

        return view('AppointmentScheduler/success');
    }

    public function schedule()
    {
        return view('AppointmentScheduler/schedule');
    }

     public function availableSlots()
    {
        helper(['form']);
        $date = $this->request->getPost('date');
        $startTime = strtotime('09:00');
        $endTime = strtotime('17:00');
        $lunchStart = strtotime('13:00');
        $lunchEnd = strtotime('14:00');
        $interval = 30 * 60; 
        $slots = [];
        $booked = [];
        if ($date) {
            $bookedSlots = $this->Appointment_model->getBookedSlots($date); 
            $booked = array_column($bookedSlots, 'time'); print_r($booked); 
        }
        for ($time = $startTime; $time < $endTime; $time += $interval) {
            if ($time >= $lunchStart && $time < $lunchEnd) continue;
            $slot = date('H:i', $time);
            $slots[] = [
                'time' => $slot,
                'available' => !in_array($slot, $booked)
            ];
        }
        print_r($slots);
        return view('AppointmentScheduler/available_slots', [
            'date' => $date,
            'slots' => $slots
        ]);
    }

    public function book()
    {
        
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'date' => $this->request->getPost('date'),
            'time' => $this->request->getPost('time'),
        ];
        if($data['date']==''){ $data['date']=date('Y-m-d'); }
        if ($this->Appointment_model->isSlotAvailable($data['date'], $data['time'])) {
            $this->Appointment_model->save($data);
            return redirect()->to('/appointment/schedule')->with('success', 'Appointment booked successfully!');
        } else {
            return redirect()->to('/appointment/schedule')->with('error', 'This slot is already booked.');
        }
    }



}