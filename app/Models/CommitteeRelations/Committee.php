<?php

namespace App\Models\CommitteeRelations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasFactory;
    protected $fillable = [
        'committee_id',
        'tender_id',
        'type',
    ];
    
    protected $primaryKey = 'committee_id';
    
    public function Tender(){
        return $this->belongsTo(Tender::class,'tender_id');
    }
    public function CommitteeMember(){
        return $this->hasMany(CommitteeMember::class,'tender_id');
    }
}
