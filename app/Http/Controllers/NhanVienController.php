<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Models\nhanvien;
use App\Models\chucvu;

use Image;

use Session;

class NhanVienController extends Controller
{
    public function Admin(){
        if(Session::has('tendangnhap') && Session::has('vaitro')){
            $nhanvien = nhanvien::orderBy('tendangnhap','DESC')->Paginate(3);
            return view('nhanvien.admin',compact('nhanvien'));
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function Search(Request $request){
        if($request->keyword==''){
            $nhanvien = nhanvien::orderBy('tendangnhap','DESC')->Paginate(3);
        }else{
            $tenCV = chucvu::where('tenCV',$request->keyword)->first();
            if($tenCV){
                $tenCV = chucvu::where('tenCV',$request->keyword)->get();
                foreach($tenCV as $t){}
                $nhanvien = nhanvien::where('maCV','LIKE','%'.$t->maCV.'%')->orderBy('tendangnhap','DESC')->Paginate(3);
            }else{
                $nhanvien = nhanvien::where('soDT','LIKE','%'.$request->keyword.'%')
                                    ->orwhere('tenNV','LIKE','%'.$request->keyword.'%')
                                    ->orwhere('gioitinh','LIKE','%'.$request->keyword.'%')
                                    ->orwhere('namsinh','LIKE','%'.$request->keyword.'%')
                                    ->orderBy('tendangnhap','DESC')->Paginate(3);
            }
        }
        $nhap = $request->keyword;
        return view('nhanvien.admin',compact('nhanvien','nhap'));
    }

    public function getThemNhanVien(){
        if(Session::has('tendangnhap') && Session::has('vaitro')){
            $chucvu = chucvu::all();
            return view('nhanvien.themnhanvien.themnhanvien',['chucvu' => $chucvu]);
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function kiemtrasdt($soDT){
        $nhanvien = nhanvien::all();
        foreach($nhanvien as $nv){
            if($soDT == $nv->soDT){
                echo "S??? ??i???n tho???i: S??? ??i???n tho???i ???? t???n t???i";
            }else{
                echo "";
            }
        }
    }
    public function kiemtratuoi($namsinh){
        $namsinhnhap = date_create($namsinh);
        if((date('Y') - date_format($namsinhnhap,'Y')) < 18){
            echo "N??m sinh: Ch??a ????? 18 tu???i";
        }else{
            echo "";
        }
    }

    public function postThemNhanVien(Request $request){
        if($request->has('anhnhanvien')){
            $file = $request->anhnhanvien;
            $tenfile_old = $file->getClientoriginalName();
            $tenfile_resize = Image::make($file->getRealPath());
            $tenfile_resize->resize(100,100);
            $file->move(public_path('anhnhanvien'),$tenfile_old);
            $tenfile = $tenfile_resize->save(public_path('anhnhanvien/'.$tenfile_old))->filename.".".$tenfile_resize->save(public_path('anhnhanvien/'.$tenfile_old))->extension;
        }else{
            $tenfile = "Ch??a c?? ???nh.";
        }

        if(Session::has('tendangnhap') && Session::has('vaitro')){
            $nhanvien = new nhanvien();
            $nhanvien->tenNV = $request->tennhanvien;
            $nhanvien->anhnhanvien = $tenfile_resize->save(public_path('anhnhanvien/'.$tenfile))->filename.".".$tenfile_resize->save(public_path('anhnhanvien/'.$tenfile))->extension;
            $nhanvien->namsinh = $request->namsinh;
            $nhanvien->gioitinh = $request->gioitinh;
            $nhanvien->matkhau = $request->matkhau;
            $nhanvien->diachi = $request->diachi;
            $nhanvien->soDT = $request->soDT;
            $nhanvien->ngayvaolam = $request->ngayvaolam;
            $nhanvien->maCV = $request->chucvu;
            $nhanvien->save();
            $nhanvien = nhanvien::orderBy('tendangnhap','DESC')->get();
            return redirect()->route('admin.nhanvien',compact('nhanvien'))->with('success-themnhanvien','Th??m nh??n vi??n th??nh c??ng!');
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function getSuaNhanVien($tendangnhap){
        if(Session::has('tendangnhap') && Session::has('vaitro')){
            $chucvu = chucvu::all();
            $nhanvien = nhanvien::where('tendangnhap',$tendangnhap)->get();
            return view('nhanvien.suanhanvien.suanhanvien',['chucvu' => $chucvu,'nhanvien' => $nhanvien]);
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function postSuaNhanVien(Request $request, $tendangnhap){
        if($request->has('anhnhanvien')){
            $file = $request->anhnhanvien;
            $tenfile_old = $file->getClientoriginalName();
            $tenfile_resize = Image::make($file->getRealPath());
            $tenfile_resize->resize(100,100);
            $file->move(public_path('anhnhanvien'),$tenfile_old);
            $tenfile = $tenfile_resize->save(public_path('anhnhanvien/'.$tenfile_old))->filename.".".$tenfile_resize->save(public_path('anhnhanvien/'.$tenfile_old))->extension;
        }else{
            $nhanvien = nhanvien::where('tendangnhap',$tendangnhap)->get();
            foreach($nhanvien as $nv){}
            $tenfile = $nv->anhnhanvien;
        }

        if(Session::has('tendangnhap') && Session::has('vaitro')){
            $nhanvien = nhanvien::where('tendangnhap',$tendangnhap)->update([
                'tenNV' => $request->tennhanvien,
                'anhnhanvien' => $tenfile,
                'namsinh' => $request->namsinh,
                'gioitinh' => $request->gioitinh,
                'matkhau' => $request->matkhau,
                'diachi' => $request->diachi,
                'soDT' => $request->soDT,
                'ngayvaolam' => $request->ngayvaolam,
                'maCV' => $request->chucvu
            ]);
            $nhanvien = nhanvien::orderBy('tendangnhap','DESC')->get();
            return redirect()->route('admin.nhanvien',compact('nhanvien'))->with('success-themnhanvien','S???a nh??n vi??n th??nh c??ng!');
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function XoaNhanVien($tendangnhap){
        if(Session::has('tendangnhap') && Session::has('vaitro')){
            nhanvien::where('tendangnhap',$tendangnhap)->delete();
            $nhanvien = nhanvien::orderBy('tendangnhap','DESC')->get();
            return redirect()->route('admin.nhanvien',compact('nhanvien'))->with('success-themnhanvien','X??a nh??n vi??n th??nh c??ng!');
        }else{
            return redirect()->route('dangnhap');
        }
    }
}
