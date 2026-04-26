<?php

namespace App\Services;
use App\Models\JobTitle;


class JobTitleService
{
    public function getAllJobTitles()
    {
        return JobTitle::all();
    }
   
    public function createJobTitle(array $data)
    {
        return JobTitle::create($data);
    }
    public function deleteJobTitle(int $id)
    {
        $jobTitle = JobTitle::find($id);
        $jobTitle->delete();
        return $jobTitle;
    }
}