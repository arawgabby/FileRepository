@extends('staff.dashboard.staffDashboard')
@section('title', 'Upload New File')
@section('content')

<div class="container mx-auto p-6 bg-white  shadow-md">

    <h1 class="-m-6 mb-6 pb-2 text-3xl font-bold border-b border-gray-300 p-6">
        Upload New File
    </h1>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Left Column - Form Fields -->
        <div class="w-full md:w-1/2">
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="category" class="block text-lg font-bold text-gray-700">Enter File Category Type</label>
                    <select name="category" id="category" class="mt-1 p-2 border rounded w-full" required>
                        <option value="">Select Category</option>
                        <option value="capstone">Capstone</option>
                        <option value="thesis">Thesis</option>
                        <option value="faculty_request">Faculty Request</option>
                        <option value="accreditation">Accreditation</option>
                        <option value="admin_docs">Admin Documents</option>
                        <option value="custom_location">Custom Location</option>
                    </select>
                </div>

                <!-- Accreditation Extra Fields (hidden by default) -->
                <div class="mb-4" id="accreditationFields" style="display: none;">
                    <label class="block text-lg font-bold text-gray-700 mb-2">Accreditation Details</label>
                    <select name="level" id="level" class="mt-1 p-2 border rounded w-full mb-2">
                        <option value="">Select Level</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>

                    <select name="phase" id="phase" class="mt-1 p-2 border rounded w-full mb-2" style="display: none;">
                        <option value="">Select Phase</option>
                        <option value="Phase 1">Phase 1</option>
                        <option value="Phase 2">Phase 2</option>
                    </select>

                    <select name="area" id="area" class="mt-1 p-2 border rounded w-full mb-2">
                        <option value="">Select Area</option>
                        <option value="1">1-VISION, MISION, GOALS AND OBJECTIVES</option>
                        <option value="2">2-FACULTY</option>
                        <option value="3">3-CURRICULUM AND INSTRUCTIONS</option>
                        <option value="4">4-SUPPORT TO STUDENTS</option>
                        <option value="5">5-RESEARCH</option>
                        <option value="6">6-EXTENSION AND COMMUNITY ENVOLVEMENT</option>
                        <option value="7">7-LIBRARY</option>
                        <option value="8">8-PHYSICAL PLANT AND FACILITIES</option>
                        <option value="9">9-LABORATORIES</option>
                        <option value="10">10-ADMINISTRATION</option>
                    </select>
                    <select name="character" id="character" class="mt-1 p-2 border rounded w-full" style="">
                        <option value="">Select Parameter</option>
                        <option value="A"> A </option>
                        <option value="B"> B</option>
                        <option value="C"> C</option>
                        <option value="D"> D</option>
                        <option value="E"> E</option>
                        <option value="F"> F</option>
                        <option value="G"> G</option>
                        <option value="H"> H</option>
                        <option value="I"> I</option>
                        <option value="J"> J</option>
                        <option value="K"> K</option>
                        <option value="L"> L</option>
                        <option value="M"> M</option>
                        <option value="N"> N</option>
                        <option value="O"> O</option>
                        <option value="P"> P</option>
                        <option value="Q"> Q </option>
                        <option value="R"> R</option>
                        <option value="S"> S</option>
                        <option value="T"> T</option>
                        <option value="U"> U </option>
                        <option value="V"> V</option>
                        <option value="W"> W</option>
                        <option value="X"> X</option>
                        <option value="Y"> Y</option>
                        <option value="Z"> Z</option>
                    </select>
                    <select name="parameter" id="parameter" class="mt-1 p-2 border rounded w-full mb-2">
                        <option value="">Select Category</option>
                        <option value="System">System</option>
                        <option value="Input">Input</option>
                        <option value="Output">Output</option>
                    </select>
                    <!-- Character A-Z (hidden by default, shown when parameter is selected) -->

                    <div class="mb-4" id="">
                        <label for="subparam" class="block text-lg font-bold text-gray-700">Sub-parameter</label>
                        <input type="text" name="subparam" id="subparam" class="p-2 border rounded w-full" placeholder="Enter Sub-">
                    </div>
                </div>

                <!-- Authors Field (hidden by default) -->
                <div class="mb-4" id="authorsField" style="display: none;">
                    <label for="authors" class="block text-lg font-bold text-gray-700">Authors</label>
                    <input type="text" name="authors" id="authors" class="p-2 border rounded w-full" placeholder="Enter authors, separated by comma">
                </div>

                <div class="mb-4" id="folderField">
                    <select name="folder" id="folder" class="mt-1 p-2 border rounded w-full">
                        <option value="">Root (uploads/)</option>
                        @foreach($subfolders as $folder)
                        @if($folder->status === 'private' && !$folder->user_has_access)
                        <option value="{{ $folder->name }}" disabled class="text-red-500">
                            {{ $folder->name }} (Private – cannot insert file)
                        </option>
                        @else
                        <option value="{{ $folder->name }}">
                            {{ $folder->name }}{{ $folder->status === 'private' ? ' (Private – access approved)' : '' }}
                        </option>
                        @endif
                        @endforeach
                    </select>

                    <span id="folderNote" class="text-sm text-gray-500 hidden">Folder selection is only required for Custom Location. For other categories, subfolders will be created automatically if needed.</span>
                </div>

                <div class="mb-4" id="publishedByField">
                    <label for="published_by" class="block text-lg font-bold text-gray-700">Published By (Optional)</label>
                    <input type="text" name="published_by" id="published_by" class="p-2 border rounded w-full" value="{{ auth()->user()->name }}">
                </div>

                <div class="mb-4">
                    <label for="year_published" class="block text-lg font-bold text-gray-700">Year Published</label>
                    <input type="number" name="year_published" id="year_published" class="p-2 border rounded w-full"
                        required min="1900" max="{{ date('Y') }}" placeholder="YYYY">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-lg font-bold text-gray-700">Description</label>
                    <textarea name="description" id="description" class="p-2 border rounded w-full" rows="3"
                        placeholder="Enter file description..."></textarea>
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full md:w-auto">
                    Upload File
                </button>
            </form>
        </div>

        <!-- Right Column - Drag & Drop File Upload -->
        <div class="w-full md:w-1/2 flex flex-col items-center">
            <div id="dropArea"
                class="mb-4 flex flex-col items-center justify-center border-2 border-dashed border-gray-400 p-6
             rounded-lg cursor-pointer bg-gray-100 w-full h-64">
                <p class="text-gray-600">Drag & Drop your file here or click to select</p>
                <input type="file" name="file" id="file" class="hidden" required>
            </div>

            <div class="text-gray-600 text-1xl mb-4 text-center">
                <p><strong>Allowed files:</strong> PPT, DOCX, PNG, SVG, PDF</p>
                <p>File Upload limited to <strong>500MB only</strong></p>
            </div>

            <!-- File Details Display (Initially Hidden) -->
            <div id="fileDetails" class="text-gray-600 hidden">
                <p><strong>File Name:</strong> <span id="fileName"></span></p>
                <p><strong>File Type:</strong> <span id="fileType"></span></p>
                <p><strong>File Size:</strong> <span id="fileSize"></span></p>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropArea = document.getElementById("dropArea");
        const fileInput = document.getElementById("file");
        const fileDetails = document.getElementById("fileDetails");
        const categorySelect = document.getElementById("category");
        const accreditationFields = document.getElementById("accreditationFields");
        const publishedByInput = document.getElementById("published_by");
        const authorsField = document.getElementById("authorsField");
        const folderSelect = document.getElementById("folder");
        const folderNote = document.getElementById("folderNote");

        const parameterSelect = document.getElementById("parameter");
        const characterSelect = document.getElementById("character");
        const folderField = document.getElementById("folderField");

        const areaSelect = document.getElementById("area");
        const levelSelect = document.getElementById("level");
        const phaseSelect = document.getElementById("phase");

        // Store all area options for reset
        const fullAreaOptions = [{
                value: "",
                text: "Select Area"
            },
            {
                value: "1",
                text: "1-VISION, MISSION, GOALS AND OBJECTIVES"
            },
            {
                value: "2",
                text: "2-FACULTY"
            },
            {
                value: "3",
                text: "3-CURRICULUM AND INSTRUCTIONS"
            },
            {
                value: "4",
                text: "4-SUPPORT TO STUDENTS"
            },
            {
                value: "5",
                text: "5-RESEARCH"
            },
            {
                value: "6",
                text: "6-EXTENSION AND COMMUNITY EVOLVEMENT"
            },
            {
                value: "7",
                text: "7-LIBRARY"
            },
            {
                value: "8",
                text: "8-PHYSICAL PLANT AND FACILITIES"
            },
            {
                value: "9",
                text: "9-LABORATORIES"
            },
            {
                value: "10",
                text: "10-ADMINISTRATION"
            }
        ];

        const phase2AreaOptions = [{
                value: "",
                text: "Select Area"
            },
            {
                value: "2",
                text: "2-FACULTY"
            },
            {
                value: "3",
                text: "3-CURRICULUM AND INSTRUCTIONS"
            },
            {
                value: "6",
                text: "6-EXTENSION AND COMMUNITY EVOLVEMENT"
            },
            {
                value: "7",
                text: "7-LIBRARY"
            },
            {
                value: "11",
                text: "LICENSURE EXAM"
            },
            {
                value: "12",
                text: "CONSORTIA OR LINKAGES"
            },
        ];

        function setAreaOptions(options) {
            areaSelect.innerHTML = "";
            options.forEach(opt => {
                let option = document.createElement("option");
                option.value = opt.value;
                option.text = opt.text;
                areaSelect.appendChild(option);
            });
        }

        phaseSelect.addEventListener("change", function() {
            if (this.value === "Phase 2") {
                setAreaOptions(phase2AreaOptions);
            } else {
                setAreaOptions(fullAreaOptions);
            }
            areaSelect.selectedIndex = 0;
        });

        parameterSelect.addEventListener("change", function() {
            if (this.value !== "") {
                characterSelect.style.display = "block";
            } else {
                characterSelect.style.display = "none";
                characterSelect.selectedIndex = 0;
            }
        });

        folderSelect.disabled = true;

        levelSelect.addEventListener("change", function() {
            if (this.value !== "") {
                phaseSelect.style.display = "block";
            } else {
                phaseSelect.style.display = "none";
                phaseSelect.selectedIndex = 0;
            }
        });

        categorySelect.addEventListener("change", function() {
            if (!this.value) {
                // If no category is selected, disable folder selection and show note
                folderField.style.display = "block";
                folderSelect.disabled = true;
                folderNote.classList.remove("hidden");
                accreditationFields.style.display = "none";
                authorsField.style.display = "none";
                publishedByInput.readOnly = true;
                publishedByInput.value = "{{ auth()->user()->name }}";
                document.getElementById("level").selectedIndex = 0;
                document.getElementById("area").selectedIndex = 0;
                document.getElementById("parameter").selectedIndex = 0;
                return;
            }


            if (this.value === "accreditation") {
                accreditationFields.style.display = "block";
                authorsField.style.display = "none";
                publishedByInput.readOnly = true;
                publishedByInput.value = "{{ auth()->user()->name }}";
                folderSelect.value = "";
                folderSelect.disabled = true;
                folderNote.classList.remove("hidden");
            } else if (
                this.value === "capstone" ||
                this.value === "thesis" ||
                this.value === "faculty_request" ||
                this.value === "admin_docs"
            ) {
                accreditationFields.style.display = "none";
                authorsField.style.display = "block";
                document.getElementById("level").selectedIndex = 0;
                document.getElementById("area").selectedIndex = 0;
                document.getElementById("parameter").selectedIndex = 0;
                publishedByInput.readOnly = false;
                publishedByInput.value = "";
                folderField.style.display = "block";
                folderSelect.value = "";
                folderSelect.disabled = true;
                folderNote.classList.remove("hidden");
            } else if (this.value === "custom_location") {
                accreditationFields.style.display = "none";
                authorsField.style.display = "none";
                document.getElementById("level").selectedIndex = 0;
                document.getElementById("area").selectedIndex = 0;
                document.getElementById("parameter").selectedIndex = 0;
                publishedByInput.readOnly = true;
                publishedByInput.value = "{{ auth()->user()->name }}";
                folderField.style.display = "block";
                folderSelect.disabled = false;
                folderNote.classList.remove("hidden");
            } else {
                accreditationFields.style.display = "none";
                authorsField.style.display = "none";
                document.getElementById("level").selectedIndex = 0;
                document.getElementById("area").selectedIndex = 0;
                document.getElementById("parameter").selectedIndex = 0;
                publishedByInput.readOnly = true;
                publishedByInput.value = "{{ auth()->user()->name }}";
                folderField.style.display = "block";
                folderSelect.disabled = false;
                folderNote.classList.add("hidden");
            }
        });

        dropArea.addEventListener("click", () => fileInput.click());

        fileInput.addEventListener("change", handleFileSelect);

        dropArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropArea.classList.add("border-blue-500");
        });

        dropArea.addEventListener("dragleave", () => dropArea.classList.remove("border-blue-500"));

        dropArea.addEventListener("drop", (e) => {
            e.preventDefault();
            dropArea.classList.remove("border-blue-500");

            let files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });

        function handleFileSelect() {
            let file = fileInput.files[0];
            if (file) {
                let fileSizeInMB = (file.size / (1024 * 1024)).toFixed(2);

                document.getElementById("fileName").textContent = file.name;
                document.getElementById("fileType").textContent = file.type || "Unknown";
                document.getElementById("fileSize").textContent = fileSizeInMB + " MB";

                fileDetails.classList.remove("hidden");
            }
        }

        $('#uploadForm').submit(function(e) {
            e.preventDefault();

            let selectedCategory = $('#category').val();
            let selectedFolder = $('#folder').val();

            // Only require folder if category is custom_location
            if (selectedCategory === "custom_location" && selectedFolder === "") {
                Swal.fire({
                    title: "Folder Required",
                    text: "Please select a specific folder (not Root) for Custom Location.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            let formData = new FormData(this);
            let fileInput = document.getElementById("file");

            if (fileInput.files.length === 0) {
                Swal.fire({
                    title: "No file selected",
                    text: "Please choose a file to upload.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            formData.append("file", fileInput.files[0]);

            if (categorySelect.value === "accreditation") {
                formData.append("level", document.getElementById("level").value);
                formData.append("phase", document.getElementById("phase").value);
                formData.append("area", document.getElementById("area").value);
                formData.append("parameter", document.getElementById("parameter").value);
                // Append character if parameter is selected
                if (parameterSelect.value !== "") {
                    formData.append("character", characterSelect.value);
                }
                // Do NOT append folder for accreditation
                formData.delete("folder");
            } else {
                // Append folder if not accreditation
                formData.set("folder", folderSelect.value);
            }

            // Append authors if visible
            if (categorySelect.value === "capstone" || categorySelect.value === "thesis") {
                formData.append("authors", document.getElementById("authors").value);
            }

            let uploadUrl = "{{ route('staff.uploadFile') }}";

            $.ajax({
                url: uploadUrl,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    Swal.fire({
                        title: "Uploading...",
                        text: "Please wait while your file is being uploaded.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON?.message || "File upload failed.";
                    Swal.fire({
                        title: "Error!",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonColor: "#d33",
                        confirmButtonText: "Try Again"
                    });
                }
            });
        });
    });
</script>

@endsection