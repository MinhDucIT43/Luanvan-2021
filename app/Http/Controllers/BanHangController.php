<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

use App\Models\nhanvien;
use App\Models\ban;
use App\Models\chucvu;
use App\Models\sanpham;
use App\Models\mon;
use App\Models\nhommon;
use App\Models\ve;
use App\Models\order;
use App\Models\hoadon;
use App\Models\chitiet_hd_thu;

use Carbon\Carbon;

use Session;

class BanHangController extends Controller
{
    public function BanHang(){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            $ban = ban::orderBy('maban','ASC')->paginate(16,'*','bp');
            return view('banhang',['ban'=>$ban]);
        }else {
            return redirect()->route('dangnhap');
        }
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            return view('banhang',compact('ban'));
        }else {
            return redirect()->route('dangnhap');
        }
    }

    public function BanHangAll(){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            $ban = ban::orderBy('maban','ASC')->paginate(16,'*','bp');
            $manuoc = nhommon::where('tenNM','LIKE','%'.'nước'.'%')->pluck('maNM');
            $nuoc = mon::whereIn('maNM',$manuoc)->paginate(5,'*','np');
            $mathit = nhommon::where('tenNM','LIKE','%'.'thịt'.'%')
                                ->orwhere('tenNM','LIKE','%'.'hải sản'.'%')
                                ->orwhere('tenNM','LIKE','%'.'lẩu'.'%')
                                ->orwhere('tenNM','LIKE','%'.'canh'.'%')
                                ->orwhere('tenNM','LIKE','%'.'truyền thống'.'%')
                                ->pluck('maNM');
            $thit = mon::whereIn('maNM',$mathit)->paginate(11,'*','tp');
            $vebuffet = ve::orderBy('mave','ASC')->paginate(4,'*','vp');
            return view('banhang.banhangall',['ban'=>$ban,'nuoc'=>$nuoc,'thit'=>$thit,'vebuffet'=>$vebuffet]);
        }else {
            return redirect()->route('dangnhap');
        }
    }

    public function BanHangVeBuffet(){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            $ban = ban::orderBy('maban','ASC')->paginate(16,'*','bp');
            $vebuffet = ve::orderBy('mave','ASC')->paginate(4,'*','vp');
            return view('banhang.banhangvebuffet',['ban'=>$ban,'vebuffet'=>$vebuffet]);
        }else {
            return redirect()->route('dangnhap');
        }
    }

    public function BanHangMonAn(){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            $ban = ban::orderBy('maban','ASC')->paginate(16,'*','bp');
            $mathit = nhommon::where('tenNM','LIKE','%'.'thịt'.'%')
                                ->orwhere('tenNM','LIKE','%'.'hải sản'.'%')
                                ->orwhere('tenNM','LIKE','%'.'lẩu'.'%')
                                ->orwhere('tenNM','LIKE','%'.'canh'.'%')
                                ->orwhere('tenNM','LIKE','%'.'truyền thống'.'%')
                                ->pluck('maNM');
            $thit = mon::whereIn('maNM',$mathit)->paginate(11,'*','tp');
            return view('banhang.banhangmonan',['ban'=>$ban,'thit'=>$thit]);
        }else {
            return redirect()->route('dangnhap');
        }
    }

    public function BanHangThucUong(){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            $ban = ban::orderBy('maban','ASC')->paginate(16,'*','bp');
            $manuoc = nhommon::where('tenNM','LIKE','%'.'nước'.'%')->pluck('maNM');
            $nuoc = mon::whereIn('maNM',$manuoc)->paginate(11,'*','np');
            return view('banhang.banhangnuocuong',['ban'=>$ban,'nuoc'=>$nuoc]);
        }else {
            return redirect()->route('dangnhap');
        }
    }

    public function BanSo($maban){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            $banso = ban::where('maban',$maban)->get();
            return view('banhang.chitietban',['banso'=>$banso]);
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function BanSoVe($maban){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            $banso = ban::where('maban',$maban)->get();
            $thanhtien = order::where('maban',$maban)->sum('thanhtien');
            $trangthai = ban::where('maban',$maban)->where('trangthai',"Có khách")->first();
            if($trangthai){
                $vechon = order::where('maban',$maban)->get('mave');
                foreach($vechon as $v){}
                $mon199 = mon::where('mave',$v->mave)->get();
                $mon269 = mon::where('mave',13)->orwhere('mave',14)->get();
                $mon319 = mon::where('mave',13)->orwhere('mave',14)->orwhere('mave',15)->get();
                $vebuffet = ve::where('mave',$v->mave)->get();
                switch($v->mave){
                    case 13: return view('banhang.chitietban199',compact('banso','thanhtien','mon199','vebuffet')); break;
                    case 14: return view('banhang.chitietban269',compact('banso','thanhtien','mon269')); break;
                    case 15: return view('banhang.chitietban319',compact('banso','thanhtien','mon319')); break;
                }
            }else{
                return view('banhang.chitietbanve',['banso'=>$banso,'vebuffet'=>$vebuffet,'thanhtien'=>$thanhtien]);
            }
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function postThemVe(Request $request){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            //dd($request->mave);
            $vebuffet = ve::orderBy('mave','DESC')->paginate(5);
            $gia = ve::where('mave',$request->mave)->get('gia');
            $banso = ban::where('maban',$request->maban)->get();
            $order = order::where('maban',$request->maban)->where('mave',$request->mave)->first();
            if($order){
                $order = order::where('maban',$request->maban)->where('mave',$request->mave)->get();
                foreach($order as $o){}
                foreach($gia as $g){}
                $soluongmoi = $o->soluong + $request->soluong;
                $thanhtienmoi = $o->thanhtien + ($request->soluong*$g->gia);
                order::where('maban',$request->maban)->where('mave',$request->mave)->update([
                    'soluong' => $soluongmoi,
                    'thanhtien' => $thanhtienmoi,
                ]);
            }else{
                $order = new order();
                $order->soluong = $request->soluong;
                foreach($gia as $g){}
                $order->thanhtien = $request->soluong*$g->gia;
                $order->maban =$request->maban;
                $order->mave = $request->mave;
                ban::where('maban',$request->maban)->update([
                    'trangthai' => "Có khách",
                ]);
                $order->save();
            }
            $thanhtien = order::where('maban',$maban)->sum('thanhtien');
            $vechon = $request->mave;
            $mon199 = mon::where('mave',$vechon)->get();
            $mon269 = mon::where('mave',13)->orwhere('mave',14)->get();
            $mon319 = mon::where('mave',13)->orwhere('mave',14)->orwhere('mave',15)->get();
            switch($vechon){
                case 13: return view('banhang.chitietban199',compact('banso','thanhtien','mon199')); break;
                case 14: return view('banhang.chitietban269',compact('banso','thanhtien','mon269')); break;
                case 15: return view('banhang.chitietban319',compact('banso','thanhtien','mon319')); break;
            }
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function postThemMonChon(Request $request){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            $mon = mon::orderBy('mamon','DESC')->paginate(9);
            $banso = ban::where('maban',$request->maban)->get();
            $order = order::where('maban',$request->maban)->where('mamon',$request->mamon)->first();
            if($order){
                $order = order::where('maban',$request->maban)->where('mamon',$request->mamon)->get();
                foreach($order as $o){}
                $soluongmoi = $o->soluong + $request->soluong;
                order::where('maban',$request->maban)->where('mamon',$request->mamon)->update([
                    'soluong' => $soluongmoi
                ]);
            }else{
                $order = new order();
                $order->maban =$request->maban;
                $order->mamon = $request->mamon;
                $order->soluong = $request->soluong;
                $order->save();
            }
            return view('banhang.chitietban',compact('mon','banso'));
        }else{
            return redirect()->route('dangnhap');
        }
    }

    public function XoaOrder($maban,$mamon){
        if(Session::get('tendangnhap') && Session::get('vaitro')){
            order::where('maban',$maban)->where('mamon',$mamon)->delete();
            $mon = mon::orderBy('mamon','DESC')->paginate(9);
            $banso = ban::where('maban',$maban)->get();
            return view('banhang.chitietban',compact('banso','mon'));
        }else{
            return redirect()->route('dangnhap');
        }
    }

    // public function postThanhToan(Request $request){
    //     if(Session::get('tendangnhap') && Session::get('vaitro')){
    //         $datenow = Carbon::now('Asia/Ho_Chi_Minh');
    //         $mahd = random_int(0,9999);
    //         $kiemtrahd = hoadon::where('MaHD_Thu',$mahd)->first();
    //         if($kiemtrahd){
    //             while(!$kiemtrahd){
    //                 $mahd = random_int(0,9999);
    //             }
    //         }
    //         $hoadon = new hoadon();
    //         $hoadon->MaHD_Thu = $mahd;
    //         $hoadon->TongTien = $request->thanhtien;
    //         $hoadon->TenDangNhap = Session::get('tendangnhap');
    //         $hoadon->MaBan = $request->maban;
    //         $hoadon->NgayLap = $datenow->toDateString();
    //         $hoadon->save();
    //         $temp = temp::where('MaBan',$request->maban)->get();
    //         foreach($temp as $t){
    //             $chitiethd = new chitiet_hd_thu();
    //             $chitiethd->MaHD_Thu = $mahd;
    //             $chitiethd->MaMon = $t->MaMon;
    //             $chitiethd->soluong = $t->soluong;
    //             $mon = mon::where('MaMon',$t->MaMon)->get();
    //             foreach($mon as $m){}
    //             $chitiethd->DonGia = $m->Gia;
    //             $chitiethd->ThanhTien = ($t->soluong)*($m->Gia);
    //             $chitiethd->save();
    //         }
    //         temp::where('MaBan',$request->maban)->delete();
    //         $trusl = chitiet_hd_thu::where('MaHD_Thu',$mahd)->get();
    //         foreach($trusl as $tru){
    //             $truslmon = mon::where('MaMon',$tru->MaMon)->get();
    //             foreach($truslmon as $slmon){
    //                 $soluong = $slmon->soluong;
    //             }
    //             $soluongmoi = $soluong - $tru->soluong;
    //             mon::where('MaMon',$tru->MaMon)->update([
    //                 'soluong' => $soluongmoi
    //             ]);
    //         }
    //         $mon = mon::orderBy('MaMon','ASC')->paginate(10);
    //         $data = ban::orderBy('MaBan','ASC')->get();
    //         return view('banhang.admin',compact('mon','data'));
    //     }else{
    //         return redirect()->route('showlogin');
    //     }
    // }
}