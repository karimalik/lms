<div>
    <div class="pathway_section">

            <div class="">
                    Date Enrolled : {{showDate($enrolld->created_at)}}
            </div>
            @if ($enrolld->enrolled_by!=null)   
                <div class="">
                        Enrolled By : {{@$enrolld->enrolledBy->name}}
                </div>
            @endif
            @if ($enrolld->pathway_id!=null)   
                <div class="">
                        Pathway : {{@$enrolld->pathway->name}}
                </div>
            @endif
    </div>
</div>