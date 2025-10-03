@extends('layouts.user')

@section('content2')

<section >
<form class="max-w-sm mx-auto border-r border-t border-l border-gray-700 rounded-xl p-10 bg-gray-700 shadow-xl shadow-black " action="{{route('Profile.update',$User->id)}}" method="POST" >
    @csrf
    @method('PUT')
    <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-center text-white">Edit User</h2>
  <div class="mb-5">
    <label  class="block mb-2 font-medium text-bold text-white"><b> User Name</b></label>
    <input type="text" name="name" class="bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-white text-white focus:ring-blue-500 dark:focus:border-blue-500" value="{{ old('name', $User->name) }}" placeholder=""  />
  </div>
  <div class="mb-5">
    <label  class="block mb-2 font-medium text-white">Email</label>
    <input type="email" name="email"  class="bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-white text-white focus:ring-blue-500 dark:focus:border-blue-500" value="{{ old('email', $User->email) }}"  />
  </div>
  <div class="mb-5">
    <label  class="block mb-2 font-medium text-white">password</label>
    <input type="password" name="password"  class="bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-white text-white focus:ring-blue-500 dark:focus:border-blue-500"  />
  </div>
  <div class="mb-5">
    <label  class="block mb-2 font-medium text-white">password_confirmation</label>
    <input type="password" name="password_confirmation"  class="bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-white text-white focus:ring-blue-500 dark:focus:border-blue-500"   />
  </div>
 
  <button type="submit" class="text-white bg-blue-700 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Submit</button>
</form>
</section>
@endsection