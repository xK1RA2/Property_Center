<!-- التعليقات -->
@props(['Comment'])
       
                <ul class="list-group list-group-flush mb-4">

                
                    <li class="list-group-item bg-light p-3 rounded mb-2 d-flex align-items-center gap-3 border">
                        <img src="https://png.pngtree.com/png-vector/20220807/ourmid/pngtree-man-avatar-wearing-gray-suit-png-image_6102786.png" height="60px">
                        <div class="d-flex flex-column">
                            @if($Comment->comment_type_id == 1)
                            <strong class="text-dark">{{$Comment->user->name}}</strong>
                            @else
                            <strong class="text-dark">Ananymous</strong>
                            @endif
                            <span class="text-muted">{{$Comment->Description}}</span>
                        </div>
                    </li>
              
                </ul>
                