<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Recrutamento/Save')}}" method="POST">
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
                    @if(!empty($id))
                    <input type="hidden" name="id" value="{{$id}}">
                    @endif
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome</label>
                            <input type="text" name="Nome" value="{{isset($Registro) ? $Registro->Nome : ''}}" class="form-control">
                        </div>
                        <div class="col-sm-4">
                            <label>Data Limite de Inscrições</label>
                            <input type="datetime-local" name="DTInscricoes" value="{{isset($Registro) ? $Registro->DTInscricoes : ''}}" class="form-control">
                        </div>
                        <div class="col-sm-4">
                            <label>Situação do processo</label>
                            <select name="STProcesso" class="form-control">
                                <option value="1" {{isset($Registro) && $Registro->STProcesso == 1 ? 'selected' : ''}}>Ativo</option>
                                <option value="0" {{isset($Registro) && $Registro->STProcesso == 0 ? 'selected' : ''}}>Inativo</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Descrição do Processo</label>
                            <textarea name="Descricao" class="form-control">{{isset($Registro) ? $Registro->Descricao : ''}}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Recrutamento/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>>
</x-educacional-layout>