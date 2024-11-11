<x-educacional-layout>
    <style>
        /* Estilo para o grid */
        #imageGrid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        .image-container {
            position: relative;
            width: 100%;
            padding-top: 100%; /* Para tornar quadrado */
        }
        .image-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        /* Estilo para o grid */
        #pdfGrid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .pdf-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #f9f9f9;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .pdf-icon {
            font-size: 50px;
            color: #d9534f; /* Cor vermelha para o ícone de PDF */
            margin-bottom: 10px;
        }
        .pdf-name {
            font-size: 14px;
            color: #333;
            word-break: break-all;
        }
    </style>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDCurso)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
           <form action="{{route('EAD/Aulas/Save')}}" method="POST" enctype="multipart/form-data">
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
            @if(isset($Registro))
            <input type="hidden" name="id" value="{{$Registro->id}}">
            @endif
            <input type="hidden" name="IDCurso" value="{{$IDCurso}}">
            <input type="hidden" name="IDEscola" value="{{$IDEscola}}">
            <div class="col-sm-12">
                <label>Etapa</label>
                <select name="IDEtapa" class="form-control">
                    <option value="">Etapa</option>
                    @foreach($Etapas as $e)
                    <option value="{{$e->id}}">{{$e->NMEtapa}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12">
                <label>Título da Aula</label>
                <input type="text" name="NMAula" class="form-control">
            </div>
            <div class="col-sm-12">
                <label>Descrição da Aula</label>
                <textarea name="DSAula" class="form-control">{{isset($Registro) ? $Registro->DSAula : ''}}</textarea>
            </div>
            <div class="col-sm-12">
                <label>Vídeo</label>
                <input type="file" id="videoUpload" name="Video" class="form-control" accept="video/*">
                <video id="videoPreview" controls width="100%" style="display: none;"></video>
            </div>
            <div class="col-sm-12">
                <label>Imagens</label>
                <input type="file" id="imageUpload" name="Imagens[]" class="form-control" accept="image/*" multiple>
                <div id="imageGrid"></div>
            </div>
            <div class="col-sm-12">
                <label>PDF´s</label>
                <input type="file" id="pdfUpload" name="PDF[]" accept="application/pdf" class="form-control" multiple>
                <div id="pdfGrid"></div>
            </div>
            <br>
            <div class="col-auto">
                <button class="btn btn-fr">Salvar</button>
                <a href="{{route('EAD/Etapas',$IDCurso)}}" class="btn btn-light">Voltar</a>
            </div>
           </form>
        </div>
    </div>
    <script>
        //VIDEO PREVIEW
        document.getElementById('videoUpload').addEventListener('change', function(event) {
            const videoFile = event.target.files[0];
            const videoPreview = document.getElementById('videoPreview');

            // Verifica se um arquivo foi selecionado e é um vídeo
            if (videoFile && videoFile.type.startsWith('video/')) {
                // Cria uma URL temporária para o vídeo selecionado
                const videoURL = URL.createObjectURL(videoFile);
                videoPreview.src = videoURL;
                videoPreview.style.display = 'block';
                
                // Libera a URL quando o vídeo é encerrado, para evitar uso excessivo de memória
                videoPreview.onended = () => URL.revokeObjectURL(videoURL);
            } else {
                // Oculta o preview caso o arquivo não seja um vídeo válido
                videoPreview.style.display = 'none';
            }
        });
        //IMAGE PREVIEW
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const imageGrid = document.getElementById('imageGrid');
            imageGrid.innerHTML = ""; // Limpa o grid para novas imagens

            const files = event.target.files;

            // Itera sobre os arquivos selecionados e cria um preview para cada um
            for (const file of files) {
                if (file.type.startsWith('image/')) { // Verifica se é uma imagem
                    const imageURL = URL.createObjectURL(file);

                    // Cria um contêiner de imagem e define a URL
                    const container = document.createElement('div');
                    container.classList.add('image-container');

                    const img = document.createElement('img');
                    img.src = imageURL;
                    img.alt = "Preview da Imagem";

                    // Adiciona a imagem ao contêiner e ao grid
                    container.appendChild(img);
                    imageGrid.appendChild(container);

                    // Libera a URL temporária quando a imagem é carregada para economizar memória
                    img.onload = () => URL.revokeObjectURL(imageURL);
                }
            }
        });
        //PDF PREVIEW
        document.getElementById('pdfUpload').addEventListener('change', function(event) {
            const pdfGrid = document.getElementById('pdfGrid');
            pdfGrid.innerHTML = ""; // Limpa o grid para novos arquivos

            const files = event.target.files;

            // Itera sobre os arquivos selecionados e cria um card para cada PDF
            for (const file of files) {
                if (file.type === 'application/pdf') { // Verifica se é um PDF
                    const pdfURL = URL.createObjectURL(file);

                    // Cria um card para o PDF
                    const card = document.createElement('div');
                    card.classList.add('pdf-card');

                    // Ícone de PDF (pode ser substituído por uma imagem se desejar)
                    const icon = document.createElement('div');
                    icon.classList.add('pdf-icon');
                    icon.innerHTML = "📄"; // Emoji representando PDF, substitua por uma imagem se preferir

                    // Nome do PDF
                    const name = document.createElement('div');
                    name.classList.add('pdf-name');
                    name.textContent = file.name;

                    // Adiciona ícone, nome, e link para visualizar o PDF
                    card.appendChild(icon);
                    card.appendChild(name);

                    // Abre o PDF em uma nova aba ao clicar no card
                    card.addEventListener('click', () => {
                        window.open(pdfURL, '_blank');
                    });

                    // Adiciona o card ao grid
                    pdfGrid.appendChild(card);
                    
                    // Libera a URL temporária quando o card é removido para economizar memória
                    card.onremove = () => URL.revokeObjectURL(pdfURL);
                }
            }
        });
        //
    </script>
</x-educacional-layout>