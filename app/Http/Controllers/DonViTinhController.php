<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\donvitinh;
use App\Models\sanpham;

use Session;

class DonViTinhController extends Controller
{
    public function Admin(){
        if(Session::has('tendangnhap') && Session::has('vaitro')){
            $donvitinh = donvitinh::orderBy('maDVT','DESC')->Paginate(8);
            return view('donvitinh.admin',compact('donvitinh'));
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function Search(Request $request){
        if($request->keyword==''){
            $donvitinh = donvitinh::orderBy('maDVT','DESC')->Paginate(8);
        }else{
            $donvitinh = donvitinh::where('tenDVT','LIKE','%'.$request->keyword.'%')->orderBy('maDVT','DESC')->Paginate(8);
        }
        $nhap = $request->keyword;
        return view('donvitinh.admin',compact('donvitinh','nhap'));
    }

    public function getThemDonViTinh(){
        if(Session::has('tendangnhap') && Session::has('vaitro')){
            return view('donvitinh.themdonvitinh.themdonvitinh');
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function kiemtratendvt($tendonvitinh){
        $donvitinh = donvitinh::all();
        foreach($donvitinh as $dvt){
            if($tendonvitinh == $dvt->tenDVT){
                echo "Đơn vị đã tồn tại";
            }else{
                echo "";
            }
        }
    }

    public function postThemDonViTinh(Request $request){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            $donvitinh = new donvitinh();
            $donvitinh->tenDVT = $request->tendonvitinh;
            $donvitinh->save();
            $donvitinh = donvitinh::orderBy('maDVT','DESC')->get();
            return redirect()->route('admin.donvitinh',compact('donvitinh'))->with('success-themdonvitinh','Thêm đơn vị tính thành công!');
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function getSuaDonViTinh($maDVT){
        if(Session::has('tendangnhap') && Session::has('vaitro')){
            $donvitinh = donvitinh::orderBy('maDVT','DESC')->get();
            $donvitinh_tt = sanpham::where('maDVT',$maDVT)->first();
            if($donvitinh_tt){
                return redirect()->route('admin.donvitinh',compact('donvitinh'))->with('success-themdonvitinh','Tồn tại sản phẩm thuộc đơn vị tính bạn muốn sửa.');
            }else{
                $donvitinh = donvitinh::where('maDVT',$maDVT)->get();
                return view('donvitinh.suadonvitinh.suadonvitinh',['donvitinh' => $donvitinh]);
            }
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function postSuaDonViTinh(Request $request, $maDVT){
        if(Session::has('tendangnhap') && Session::has('vaitro')){
            $donvitinh = donvitinh::where('maDVT',$maDVT)->update([
                'tenDVT' => $request->tendonvitinh,
            ]);
            $donvitinh = donvitinh::orderBy('maDVT','DESC')->get();
            return redirect()->route('admin.donvitinh',compact('donvitinh'))->with('success-themdonvitinh','Sửa đơn vị tính thành công!');
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function XoaDonViTinh($maDVT){
        if(Session::has('tendangnhap') && Session::has('vaitro')){
            $donvitinh = donvitinh::orderBy('maDVT','DESC')->get();
            $donvitinh_tt = sanpham::where('maDVT',$maDVT)->first();
            if($donvitinh_tt){
                return redirect()->route('admin.donvitinh',compact('donvitinh'))->with('success-themdonvitinh','Tồn tại sản phẩm thuộc đơn vị tính bạn muốn xóa.');
            }else{
                donvitinh::where('maDVT',$maDVT)->delete();
                $donvitinh = donvitinh::orderBy('maDVT','DESC')->get();
                return redirect()->route('admin.donvitinh',compact('donvitinh'))->with('success-themdonvitinh','Xóa đơn vị tính thành công!');
            }
        }else{
            return redirect()->route('dangnhap');
        }
    }
}
