<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
        <!-- Cabeçalho da escola e nome do aluno -->
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $escola->id . '/' . $escola->foto) }}" alt="Logo da Escola" width="100">
            <h1>{{ $escola->nome }}</h1>
        </div>

        @foreach($registros as $registro)
            <div>
                <p><strong>Aluno:</strong> {{ $registro->nome }}</p>

                <!-- Tabela de Respostas -->
                <table class="table">
                    <thead>
                        <tr>
                            <th class="content-cell">Conteúdo</th>
                            <th class="response-cell">Resposta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(json_decode($registro->respostas, true) as $resposta)
                            <tr>
                                <td class="content-cell">{{$resposta['Conteudo']}}</td>
                                <td class="response-cell">{{ $resposta['Resposta'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <br><hr><br> <!-- Espaço entre boletins -->
            </div>
        @endforeach
        </div>
    </div>
</x-educacional-layout>