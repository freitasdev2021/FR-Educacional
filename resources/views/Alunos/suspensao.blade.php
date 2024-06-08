<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Alunos/Suspenso/Save')}}" method="POST">
                @csrf
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
                <div class="col-sm-12 p-2">
                    <input type="hidden" value="{{$Registro->IDAluno}}" name="IDInativo">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Inicio da Suspensão</label>
                            <input type="date" class="form-control" name="INISuspensao" value="{{!empty($Registro->INISuspensao) ? $Registro->INISuspensao : date('Y-m-d')}}" disabled> 
                        </div>
                        <div class="col-sm-6">
                            <label>Termino da Suspensão</label>
                            <input type="date" class="form-control" name="TERSuspensao" value="{{!empty($Registro->TERSuspensao) ? $Registro->TERSuspensao : ''}}" required> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Justificativa da Suspensão</label>
                            <textarea class="form-control" maxlength="250" name="Justificativa" required>{{!empty($Registro->Justificativa) ? $Registro->Justificativa : ''}}</textarea> 
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-auto">
                            <button class="btn btn-fr">Salvar</button>
                        </div>
                        @if(!empty($Registro->Justificativa))
                        <div class="col-auto">
                            <button class="btn btn-danger" type="button" onclick="removerSuspensao()">Remover Suspensão</button>
                        </div>
                        @endif
                    </div>
                </div>
            </form>
            @if(!empty($Registro->Justificativa))
            <form id="removeSuspensao" action="{{route('Alunos/Suspenso/Remove')}}" method="POST">
                @csrf
                <input type="hidden" value="{{$Registro->IDAluno}}" name="IDInativo">
                <input type="hidden" value="{{!empty($Registro->INISuspensao) ? $Registro->INISuspensao : ''}}" name="INISuspensao">
                <input type="hidden" value="{{!empty($Registro->TERSuspensao) ? $Registro->TERSuspensao : ''}}" name="TERSuspensao">
                <input type="hidden" value="{{!empty($Registro->Justificativa) ? $Registro->Justificativa : ''}}" name="Justificativa"> 
            </form>
            @endif
            <script>
                function removerSuspensao(){
                    if(confirm('Deseja Remover a Suspensão? o Aluno Poderá Voltar as Atividades Normalmente')){
                        $("#removeSuspensao").submit()
                    }
                }
            </script>
        </div>
    </div>
</x-educacional-layout>