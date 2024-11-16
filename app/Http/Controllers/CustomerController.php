<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Wallet;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function create(Request $request)
    {
        $customer = Customer::create($request->only(['name', 'email', 'phone']));

        // Buat wallet untuk customer
        $wallet = Wallet::create([
            'customer_id' => $customer->id,
            'balance' => 0 // saldo awal 0
        ]);

        return response()->json(['customer' => $customer, 'wallet' => $wallet]);
    }

    public function show($id)
    {
        $customer = Customer::with('wallet')->find($id);

        if ($customer) {
            return response()->json($customer);
        }

        return response()->json(['message' => 'Customer not found'], 404);
    }

    public function updateBalance(Request $request, $id)
    {
        $customer = Customer::find($id);

        if ($customer && $customer->wallet) {
            $customer->wallet->update(['balance' => $request->balance]);

            return response()->json(['wallet' => $customer->wallet]);
        }

        return response()->json(['message' => 'Customer or wallet not found'], 404);
    }

    public function index()
{
    // Mengambil semua data customer beserta wallet-nya
    $customers = Customer::with('wallet')->get();

    // Kirim data ke view welcome
    return view('welcome', ['customers' => $customers]);
}

public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:customers,email',
        'phone' => 'required',
    ]);

    // Buat customer baru
    $customer = Customer::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
    ]);

    // Buat wallet untuk customer tersebut
    Wallet::create([
        'customer_id' => $customer->id,
        'balance' => 0, // saldo awal 0
    ]);

    return redirect('/')->with('success', 'Customer added successfully!');
}

public function destroy($id)
{
    // Cari customer berdasarkan ID
    $customer = Customer::find($id);

    // Jika customer ditemukan, hapus
    if ($customer) {
        $customer->delete();
        return redirect('/')->with('success', 'Customer deleted successfully!');
    }

    return redirect('/')->with('error', 'Customer not found!');
}

public function edit($id)
{
    // Cari customer berdasarkan ID
    $customer = Customer::with('wallet')->find($id);

    // Jika customer ditemukan, kirim data ke view
    if ($customer) {
        return view('edit', ['customer' => $customer]);
    }

    return redirect('/')->with('error', 'Customer not found!');
}

public function update(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:customers,email,' . $id,
        'phone' => 'required',
        'balance' => 'required|numeric',
    ]);

    // Cari customer berdasarkan ID
    $customer = Customer::with('wallet')->find($id);

    if ($customer) {
        // Update data customer
        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update saldo wallet
        $customer->wallet->update([
            'balance' => $request->balance,
        ]);

        return redirect('/')->with('success', 'Customer updated successfully!');
    }

    return redirect('/')->with('error', 'Customer not found!');
}


}

