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
                <form action="{{route('Biblioteca/Save')}}" method="POST">
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
                        <div class="col-sm-{{!isset($Registro) ? '6' : '12'}}">
                            <label>Nome</label>
                            <input type="text" class="form-control" name="Nome" value="{{isset($Registro) ? $Registro->Nome : ''}}">
                        </div>
                        @if(!isset($Registro))
                        <div class="col-sm-6">
                            <label>Código de Barras(EAN-13)</label>
                            <select name="IDCodigo" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($codLivros as $cd)
                                <option value="{{$cd->id}}" {{isset($Registro) && $Registro->IDCodigo == $cd->id ? 'select' : '' }}>{{$cd->Codigo}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Autores</label>
                            <input type="text" name="Autor" class="form-control" maxlength="50" required value="{{isset($Registro) ? $Registro->Autor : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Editoras</label>
                            <input type="text" name="Editora" class="form-control" value="{{isset($Registro->Editora) ? $Registro->Editora : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Classificação</label>
                            <input type="text" name="Classificacao" class="form-control" maxlength="50" required value="{{isset($Registro->Classificacao) ? $Registro->Classificacao : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Biblioteca/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>