<!-- التعليقات -->
@props(['Comment'])
       
                <ul class="list-group list-group-flush mb-4">

                
                    <li class="list-group-item bg-light p-3 rounded mb-2 d-flex align-items-center gap-3 border">
                        <img id="profile-pic" 
                    
                    src="{{
                    asset('storage/'.$Comment->user->profile_image) 
                    }}" 
                    
                    class="img-fluid rounded-circle mx-auto d-block mt-2 shadow-sm" 
                 style="width: 100px; height: 100px; object-fit: cover;">
                           
                        <div class="" style="width:100%">
                            @if($Comment->comment_type_id == 1)
                            <strong class="text-dark d-flex fs-5">{{$Comment->user->name}}</strong>
                            @else
                            <strong class="text-dark d-flex fs-5">Ananymous</strong>
                            @endif
                            <span class="text-black  fs-5" style="">{{$Comment->Description}}</span>
                            @auth
                            @if($Comment->user->id == request()->user()->id)
                            
                               <form action="{{route('CommentDelete',$Comment)}}" class=" " style="float:right">
                                <button class="btn-danger border-none btn">Delete</button>
                            </form>
                       
                            @endauth
                            @else
                            <a href="" class="link text-info d-flex pt-3 ">Replay</a>
                            @endif
                        </div>
                    </li>
              
                </ul>
                