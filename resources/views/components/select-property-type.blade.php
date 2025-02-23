@props(['propertyType' , 'count'=>0])
@if($propertyType && isset($propertyType->name))
 <option value="{{$count++}}">{{$propertyType->name}}</option>
 @endif
