<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <!--LISTAS-->
            <form class="col-sm-12 p-2" action="{{route('Formularios/Responder')}}" method="POST">
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
                <input type="hidden" value="{{$id}}" name="IDForm">
                @foreach($Formulario as $fKey => $f)
                <br>
                    @if(count($f->Conteudos) > 0)
                        <div class="col-sm-12">
                            <label>{{$f->Conteudo}}</label>
                            @foreach($f->Conteudos as $fc)
                            <div class="d-flex">
                                <input type="radio" name="{{$fKey}}" value="{{$fc}}" class="form-check">&nbsp;{{$fc}}
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="col-sm-12">
                            <label>{{$f->Conteudo}}</label>
                            <input type="text" name="{{$fKey}}" class="form-control">
                        </div>
                    @endif
                @endforeach
                <br>
                <div>
                    <button class="btn btn-success col-auto">Enviar</button>
                    <a href='{{route('Formularios/index')}}' class="btn btn-light col-auto">Voltar</a>
                </div>
                
            </form>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>