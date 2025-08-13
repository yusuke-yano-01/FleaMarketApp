<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ContactListController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        
        // リセットフラグが設定されている場合は検索結果をクリア
        if ($request->session()->has('reset')) {
            $request->session()->forget('reset');
        }
        
        $contacts = Contact::paginate(7);
        return view('contactlist.contactlist', compact('categories', 'contacts')); 
    }

    public function search(Request $request)
    {
        $categories = Category::all();
        
        $query = Contact::query();
        
        // メールアドレスで検索
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        
        // 性別で検索
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        
        // 商品の種類（カテゴリ）で検索
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // 登録日で検索
        if ($request->filled('created_at')) {
            $date = Carbon::parse($request->created_at)->format('Y-m-d');
            $query->whereDate('created_at', $date);
        }
        
        $contacts = $query->paginate(7);

        // 検索条件をページネーションに追加
        $contacts->appends($request->query());

        return view('contactlist.contactlist', compact('categories', 'contacts'));
    }

    public function reset(Request $request)
    {
        // セッションから検索条件をクリア
        $request->session()->forget(['email', 'gender', 'category_id', 'created_at']);
        
        // リセットフラグを設定
        $request->session()->put('reset', true);
        
        // リダイレクト
        return redirect('/contactlist');
    }

    public function export(Request $request)
    {
        $query = Contact::query();
        
        // 検索条件がある場合は適用
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('created_at')) {
            $date = Carbon::parse($request->created_at)->format('Y-m-d');
            $query->whereDate('created_at', $date);
        }
        
        $contacts = $query->with('category')->get();
        
        // CSVヘッダー
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ContactsList_' . date('Y-m-d_H-i-s') . '.csv"',
        ];
        
        // BOMを追加（Excelで文字化けを防ぐ）
        $callback = function() use ($contacts) {
            $file = fopen('php://output', 'w');
            
            // BOMを出力
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // ヘッダー行
            fputcsv($file, [
                'ID',
                'お名前',
                '性別',
                'メールアドレス',
                '電話番号',
                '住所',
                '建物名',
                'お問い合わせ内容の種類',
                'お問い合わせ内容',
                '登録日時'
            ]);
            
            // データ行
            foreach ($contacts as $contact) {
                $gender = '';
                if ($contact->gender == 1) {
                    $gender = '男性';
                } elseif ($contact->gender == 2) {
                    $gender = '女性';
                } elseif ($contact->gender == 3) {
                    $gender = 'その他';
                }
                
                fputcsv($file, [
                    $contact->id,
                    $contact->last_name . ' ' . $contact->first_name,
                    $gender,
                    $contact->email,
                    $contact->tell,
                    $contact->address,
                    $contact->building ?? '',
                    $contact->category ? $contact->category->content : '',
                    $contact->detail,
                    $contact->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
}
