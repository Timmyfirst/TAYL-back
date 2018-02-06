<?php

namespace App\Jobs;

use App\JobEntity;
use App\JobsList;
use App\JobStatus;
use App\Http\Controllers\PhpLocController;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class PhpLocProcessEntity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobEntity;
    protected $urlGit;

    /**
     * Create a new job instance.
     *
     * @param JobEntity $jobEntity
     */
    public function __construct(JobEntity $jobEntity,$urlGit)
    {
        /** ajouter un paramètre */
        $this->jobEntity = $jobEntity;
        $this->urlGit = $urlGit;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $wip = JobStatus::find(2);

        Log::info("my job entity", [
            'jobentity id' => $this->jobEntity->id,
            'jobentity status' => $wip->id,
        ]);
        $phpLoc = new PhpLocController();
        $phpLoc->createPhpLocLog($this->urlGit);

        $JobEntity = JobEntity::find($this->jobEntity->id);
        $JobEntity->job_status_id = $wip->id;
        $JobEntity->save();

        Log::info("save job entity", [
            'jobentity id' => $JobEntity->id,
            'jobentity status' => $JobEntity->job_status_id,
        ]);



    }

    public function failed()
    {
        $wip = JobStatus::find(3);
        $JobEntity = JobEntity::find($this->jobEntity->id);
        $JobEntity->job_status_id = $wip->id;
        $JobEntity->save();
        Log::info("mon id job entity failed", [
            'jobentity id' => $this->jobEntity->id,
            'jobentity status' => $wip->id,
        ]);
    }
}
