<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <form action="{{route('Aulas/Atividades/setNota')}}" method="POST">
                    @csrf
                    @method("POST")
                    @if(session('success'))
                    <div class="col-sm-12 shadow p-2 bg-success text-white">
                        <strong>{{session('success')}}</strong>
                    </div>
                    @elseif(session('error'))
                    <div class="col-sm-12 shadow p-2 bg-danger text-white">
                        <strong>{{session('error')}}</strong>
                    </div>
                    <br>
                    @endif
                    <input type="hidden" name="IDAtividade" value="{{$id}}">
                    <table class="table table-sm tabela">
                        <thead>
                          <tr>
                            <th style="text-align:center;" scope="col">Aluno</th>
                            <th style="text-align:center;" scope="col">Pontuação</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tbody>
                            @foreach($Alunos as $a)
                            <tr>
                                <td><input type="hidden" name="Aluno[]" value="{{$a->IDAluno}}">{{$a->Aluno}}</td>
                                <td><input type="text" name="Pontuacao[]" value="{{$a->Pontos}}"></td>
                            </tr>
                            @endforeach
                          </tbody>
                        </tbody>
                      </table>
                      <div class="col-sm-12">
                        <button class="col-auto btn bg-fr text-white">Salvar</button>
                        <a class="btn col-auto btn-default" href="{{route('Aulas/Atividades/Edit',$id)}}">Voltar</a>
                      </div>
                </form>
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        
    </script>
</x-educacional-layout>