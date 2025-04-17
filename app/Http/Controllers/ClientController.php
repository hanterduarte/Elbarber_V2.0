<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $clients = Client::paginate(10);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.form');
    }

    public function store(Request $request)
    {
        try {
            Log::info('Tentativa de criar novo cliente', ['data' => $request->all()]);

            $validated = $request->validate([
            'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:clients,email',
            'phone' => 'required|string|max:20',
                'birth_date' => 'required|date',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:2',
                'zip_code' => 'nullable|string|max:10',
                'notes' => 'nullable|string',
                'is_active' => 'boolean'
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'email.required' => 'O campo email é obrigatório.',
                'email.email' => 'O email informado não é válido.',
                'email.unique' => 'Este email já está cadastrado.',
                'phone.required' => 'O campo telefone é obrigatório.',
                'birth_date.required' => 'O campo data de nascimento é obrigatório.',
                'birth_date.date' => 'A data de nascimento informada não é válida.'
            ]);

            Log::info('Dados validados com sucesso', ['data' => $validated]);

            $client = Client::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'birth_date' => $validated['birth_date'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip_code' => $validated['zip_code'],
                'notes' => $validated['notes'],
                'is_active' => $request->boolean('is_active')
            ]);

            Log::info('Cliente criado com sucesso', ['client_id' => $client->id]);

            return redirect()
                ->route('clients.index')
                ->with('success', 'Cliente criado com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação ao criar cliente', [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Erro ao criar cliente', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            return back()
                ->with('error', 'Erro ao criar cliente: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.form', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        try {
            Log::info('Tentativa de atualizar cliente', [
                'client_id' => $client->id,
                'data' => $request->all()
            ]);

            $validated = $request->validate([
            'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'required|string|max:20',
                'birth_date' => 'required|date',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:2',
                'zip_code' => 'nullable|string|max:10',
                'notes' => 'nullable|string',
                'is_active' => 'boolean'
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'email.required' => 'O campo email é obrigatório.',
                'email.email' => 'O email informado não é válido.',
                'email.unique' => 'Este email já está cadastrado.',
                'phone.required' => 'O campo telefone é obrigatório.',
                'birth_date.required' => 'O campo data de nascimento é obrigatório.',
                'birth_date.date' => 'A data de nascimento informada não é válida.'
            ]);

            Log::info('Dados validados com sucesso', ['data' => $validated]);

            $client->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'birth_date' => $validated['birth_date'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip_code' => $validated['zip_code'],
                'notes' => $validated['notes'],
                'is_active' => $request->boolean('is_active')
            ]);

            Log::info('Cliente atualizado com sucesso', ['client_id' => $client->id]);

            return redirect()
                ->route('clients.index')
                ->with('success', 'Cliente atualizado com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação ao atualizar cliente', [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar cliente', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            return back()
                ->with('error', 'Erro ao atualizar cliente: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Client $client)
    {
        try {
            Log::info('Tentativa de excluir cliente', ['client_id' => $client->id]);
        $client->delete();
            Log::info('Cliente excluído com sucesso', ['client_id' => $client->id]);
            return redirect()
                ->route('clients.index')
                ->with('success', 'Cliente excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir cliente', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->with('error', 'Erro ao excluir cliente: ' . $e->getMessage());
        }
    }
} 