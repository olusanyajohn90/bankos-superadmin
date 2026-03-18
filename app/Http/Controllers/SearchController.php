<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return view('search', ['q' => $q, 'tenants' => collect(), 'customers' => collect(), 'transactions' => collect(), 'accounts' => collect()]);
        }

        $like = '%' . $q . '%';

        $tenants = DB::table('tenants')
            ->where(function ($query) use ($like) {
                $query->where('name', 'ILIKE', $like)
                      ->orWhere('short_name', 'ILIKE', $like)
                      ->orWhere('email', 'ILIKE', $like);
            })
            ->select('id', 'name', 'short_name', 'status', 'email')
            ->limit(5)->get();

        $customers = DB::table('customers as c')
            ->leftJoin('tenants as t', 't.id', '=', 'c.tenant_id')
            ->where(function ($query) use ($like) {
                $query->where(DB::raw("c.first_name || ' ' || c.last_name"), 'ILIKE', $like)
                      ->orWhere('c.phone', 'ILIKE', $like)
                      ->orWhere('c.email', 'ILIKE', $like)
                      ->orWhere('c.customer_number', 'ILIKE', $like);
            })
            ->select(
                'c.id', 'c.customer_number', 'c.status', 'c.kyc_tier',
                DB::raw("c.first_name || ' ' || c.last_name AS full_name"),
                'c.phone', 'c.email',
                't.name AS tenant_name', 't.id AS tenant_id'
            )
            ->limit(10)->get();

        $accounts = DB::table('accounts as a')
            ->leftJoin('customers as c', 'c.id', '=', 'a.customer_id')
            ->leftJoin('tenants as t', 't.id', '=', 'a.tenant_id')
            ->where('a.account_number', 'ILIKE', $like)
            ->select(
                'a.id', 'a.account_number', 'a.type', 'a.status', 'a.available_balance',
                DB::raw("c.first_name || ' ' || c.last_name AS customer_name"),
                't.name AS tenant_name'
            )
            ->limit(5)->get();

        $transactions = DB::table('transactions as tx')
            ->leftJoin('tenants as t', 't.id', '=', 'tx.tenant_id')
            ->leftJoin('accounts as a', 'a.id', '=', 'tx.account_id')
            ->leftJoin('customers as c', 'c.id', '=', 'a.customer_id')
            ->where(function ($query) use ($like) {
                $query->where('tx.reference', 'ILIKE', $like)
                      ->orWhere('tx.description', 'ILIKE', $like);
            })
            ->select(
                'tx.id', 'tx.reference', 'tx.type', 'tx.amount', 'tx.status',
                'tx.created_at', 't.name AS tenant_name',
                DB::raw("COALESCE(c.first_name || ' ' || c.last_name, 'Unknown') AS customer_name")
            )
            ->limit(10)->get();

        return view('search', compact('q', 'tenants', 'customers', 'transactions', 'accounts'));
    }
}
