<?php

namespace App\Repositories\CitizenComplaint;


interface CitizenComplaintServiceInterface{

    public function createComplaint(array $data);

    public function update($complaint, array $data);

    public function myComplaints($userId);

}
