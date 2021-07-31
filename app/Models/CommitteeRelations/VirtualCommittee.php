<?php

namespace App\Models\CommitteeRelations;

use App\Models\Account\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualCommittee extends Model
{
    use HasFactory;
    protected $fillable = [
        'virtual_committee_id',
        'company_id',
        'type',
    ];
    //primaryKey is committee_id with employee_id
    protected $primaryKey = 'virtual_committee_id';
    
    public function Company(){
        return $this->belongsTo(Company::class,'company_id');
    }
    public function VirtualCommitteeTender(){
        return $this->hasMany(VirtualCommitteeTender::class,'virtual_committee_tender_id');
    }
}
