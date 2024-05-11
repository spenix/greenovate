<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted} from "vue";
import http from "@/utils/https";
import notify from "@/common/notification.js";
import moment from "moment";
import { commonFuntions } from "@/common/common-function.js";
const { isNumber, converToCurrencyFormat } = commonFuntions();
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const basicSalaries = ref(page.props.basicSalaries);
const bloodTypes = ref(page.props.bloodTypes);
const employeeTypes = ref(page.props.employeeTypes);
const departments = ref(page.props.departments);
const designations = ref(page.props.designations);
const relationshipTypes = ref(page.props.relationshipTypes);
const educationalLevels = ref(page.props.educationalLevels);
const years = ref(page.props.years);


const form = useForm({
    id: "",
    firstname: "",
    middlename: "",
    lastname: "",
    suffix: "",
    gender: "",
    blood_type: "",
    birthdate: "",
    age: "",
    employee_id: "",
    contact_no: "",
    email: "",
    employee_type: "",
    department: "",
    position: "",
    date_hired: "",
    sss: "",
    tin: "",
    pag_ibig: "",
    philhealth: "",
    rate: "",
    terminate: "N",
    reason: "",
    termination_date: "",
    familyBackgound: [
    ],
    workExperience: [
    ],
    educationalAttainment: [
    ]
});

const addNewFamilyBackgoundRow = () =>  {
    if (form.familyBackgound.length < 10) {
        form.familyBackgound.push({
            name: "",
            relationship: "",
            address: "",
            contact_no: "",
            birthdate: "",
            occupation: "",
        });
    }
    
}

const addNewWorkExperienceRow = () =>  {
    if (form.workExperience.length < 10) {
    form.workExperience.push({
            company_name: "",
            position: "",
            date_from: "",
            date_to: "",
        });
    }
}


const addNewEducationalAttainmentRow = () =>  {
    if (form.educationalAttainment.length < 10) {
    form.educationalAttainment.push({
            school_name: "",
            address: "",
            school_level: "",
            year_from: page.props.current_year,
            year_to: page.props.current_year,
        });
    }
}

const btnActionDelFamilyBackgoundRow = (i, d) => {
    if (form.familyBackgound.length >= 1) {
        var idx = form.familyBackgound.indexOf(d);
        if (idx > -1) {
            form.familyBackgound.splice(idx, 1);
        }
    } else {
        notify(
                      "Info",
                      "Oops, unable to execute action.",
                      "info"
                  );
    }
}


const btnActionDelWorkExperienceRow = (i, d) => {
    if (form.workExperience.length >= 1) {
        var idx = form.workExperience.indexOf(d);
        if (idx > -1) {
            form.workExperience.splice(idx, 1);
        }
    } else {
        notify(
                      "Info",
                      "Oops, unable to execute action.",
                      "info"
                  );
    }
}

const btnActionDelEducationalAttainmentRow = (i, d) => {
    if (form.educationalAttainment.length >= 1) {
        var idx = form.educationalAttainment.indexOf(d);
        if (idx > -1) {
            form.educationalAttainment.splice(idx, 1);
        }
    } else {
        notify(
                      "Info",
                      "Oops, unable to execute action.",
                      "info"
                  );
    }
}



const resetFormAction = () => {
    form.reset();
    form.clearErrors();
};

const props = defineProps({
    modalAttrs: Object,
});

const submitData = () => {
  if (props?.modalAttrs?.action == "EDIT") {
        form.put(route('employees.update', [form.id]), {
            preserveScroll: true,
            onSuccess: () => resetFormData(),
            onError: () => {
              if (form.errors.errorMessage) {
                  notify(
                      "Error",
                      form.errors.errorMessage,
                      "danger"
                  );
                }
            },
        });
    } else {
        form.post(route('employees.store'), {
            preserveScroll: true,
            onSuccess: () => resetFormData(),
            onError: () => {
                if (form.errors.errorMessage) {
                  notify(
                      "Error",
                      form.errors.errorMessage,
                      "danger"
                  );
                }
            },
        });
    }
}

const resetFormData = () => {
    resetFormAction();
    notify(
        "Success",
        `Employee was ${
            props?.modalAttrs?.action == "EDIT" ? "updated" : "saved"
        } successfully.`,
        "success"
    );
    $(
        `#${
            props?.modalAttrs?.modalId
                ? props?.modalAttrs?.modalId
                : "page-modal"
        }`
    ).modal("hide");
    emit("reloadPageData");
};

const converToCurrency = (evt) => {
    const inputValue = evt.target.value.replace(/,/g, "");
            var val = "0.00";
            if (inputValue) {
                var val = parseFloat(inputValue).toLocaleString("en-US", {
                    style: "decimal",
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 2,
                });
            }
    form.rate = val
}
const computeEmpAge = (evt) => {
    form.age =  moment().diff(evt.target.value, 'years')
}

const getPositionBasedOnDepartment = (evt) => {
    designations.value = page.props?.designations.filter(d => {
        return d.department_id == evt.target.value
    })
}

const getBasicSalary = (evt) => {
    var result = page.props?.basicSalaries.find(d => {
        return d.designation_id == evt.target.value
    })
    form.rate = converToCurrencyFormat(result?.basic_salary ?? 0);
}

watch(props?.modalAttrs, (newValue) => {
    resetFormAction();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                form.id = data.id;
                form.firstname = data.firstname;
                form.middlename = data.middlename;
                form.lastname = data.lastname;
                form.suffix = data.suffix;
                form.gender = data.gender;
                form.blood_type = data.blood_type_id;
                form.birthdate = data.birthdate;
                form.employee_id = data.employee_no;
                form.contact_no = data.contact_no;
                form.email = data.email;
                form.employee_type = data.employee_type_id;
                form.department = data.designation_id;
                form.date_hired = data.date_hired;
                form.sss = data.sss;
                form.tin = data.tin;
                form.pag_ibig = data.pag_ibig;
                form.philhealth = data.philhealth;
                form.rate = converToCurrencyFormat(data.rate);
                form.terminate = data.terminate;
                form.reason = data.reason;
                form.termination_date = moment(data.termination_date).format('L');
                form.familyBackgound = data.family_background.map(d => { return {
                    name: d.name,
                    relationship: d.relationship_type_id,
                    address: d.address,
                    contact_no: d.contact_no,
                    birthdate: d.birthdate,
                    occupation: d.occupation,

                } })
                form.workExperience = data.work_experience.map(d => { return {
                    company_name: d.company_name,
                    position: d.position,
                    date_from: d.date_start,
                    date_to: d.date_end,

                } })
                form.educationalAttainment = data.educational_attainment.map(d => { return {
                    school_name: d.school_name,
                    address: d.address,
                    school_level: d.educational_level_id,
                    year_from: d.year_start,
                    year_to: d.year_end,
                } })
            }
        );
    }
});

</script>

<template>
    <div class="modal fade" :id="modalAttrs.modalId" tabindex="-1" data-bs-backdrop="false" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalAttrs.title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="submitData">
            <div class="modal-body" style="min-height: 480px;">
                <div class="d-flex align-items-start">
                <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                  <button class="nav-link active" id="v-pills-personal-info-tab" data-bs-toggle="pill" data-bs-target="#v-pills-personal-info" type="button" role="tab" aria-controls="v-pills-personal-info" aria-selected="true">Personal Information</button>
                  <button class="nav-link" id="v-pills-family-background-tab" data-bs-toggle="pill" data-bs-target="#v-pills-family-background" type="button" role="tab" aria-controls="v-pills-family-background" aria-selected="false" tabindex="-1">Family Background</button>
                  <button class="nav-link" id="v-pills-work-experience-tab" data-bs-toggle="pill" data-bs-target="#v-pills-work-experience" type="button" role="tab" aria-controls="v-pills-work-experience" aria-selected="false" tabindex="-1">Work Experience</button>
                  <button class="nav-link" id="v-pills-educational-attainment-tab" data-bs-toggle="pill" data-bs-target="#v-pills-educational-attainment" type="button" role="tab" aria-controls="v-pills-educational-attainment" aria-selected="false" tabindex="-1">Educational Attainment</button>
                </div>
                <div class="tab-content" id="v-pills-tabContent">
                  <div class="tab-pane fade active show" id="v-pills-personal-info" role="tabpanel" aria-labelledby="v-pills-personal-info-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Employee Personal Information</h5>
                            <div class="row">
                                <div class="col-4 mt-1">
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">First Name:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="First Name" 
                                                autocomplete="off"
                                                :class="form.errors.firstname ? 'error-field' : ''"
                                                v-model="form.firstname"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.firstname"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Middle Name:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="Middle Name" 
                                                autocomplete="off"
                                                :class="form.errors.middlename ? 'error-field' : ''"
                                                v-model="form.middlename"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.middlename"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Last Name:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="Last Name" 
                                                autocomplete="off"
                                                :class="form.errors.lastname ? 'error-field' : ''"
                                                v-model="form.lastname"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.lastname"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Suffix:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="Suffix" 
                                                autocomplete="off"
                                                :class="form.errors.suffix ? 'error-field' : ''"
                                                v-model="form.suffix"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.suffix"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Contact No.:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="Contact No." 
                                                autocomplete="off"
                                                :class="form.errors.contact_no ? 'error-field' : ''"
                                                v-model="form.contact_no"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.contact_no"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Email:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="email" 
                                                class="form-control" 
                                                placeholder="Email" 
                                                autocomplete="off"
                                                :class="form.errors.email ? 'error-field' : ''"
                                                v-model="form.email"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.email"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Date of Birth:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="date" 
                                                class="form-control" 
                                                placeholder="Date of Birth" 
                                                autocomplete="off"
                                                @change="computeEmpAge($event)"
                                                :class="form.errors.birthdate ? 'error-field' : ''"
                                                v-model="form.birthdate"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.birthdate"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mt-1">
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Age:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="Age" 
                                                autocomplete="off"
                                                :class="form.errors.age ? 'error-field' : ''"
                                                v-model="form.age"
                                                readonly
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.age"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Gender:</label>
                                        <div class="col-sm-7">
                                            <select 
                                                class="form-select" 
                                                aria-label="Select Gender"
                                                :class="form.errors.gender ? 'error-field' : ''"
                                                v-model="form.gender"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                                <option value="" hidden>
                                                    Select Gender
                                                </option>
                                                <option value="M">Male</option>
                                                <option value="F">Female</option>
                                            </select>
                                            <ErrorMessage :message="form.errors.gender"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Blood Type:</label>
                                        <div class="col-sm-7">
                                            <select 
                                                class="form-select" 
                                                aria-label="Select Blood Type"
                                                :class="form.errors.blood_type ? 'error-field' : ''"
                                                v-model="form.blood_type"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                                <option value="" hidden>
                                                    Select Blood Type
                                                </option>
                                                <option 
                                                v-for="(
                                                        d, i
                                                    ) in bloodTypes"
                                                    :key="d.id"
                                                    :value="d.id"
                                                    :selected="
                                                        form.blood_type ==
                                                        d?.id
                                                    "
                                                >{{ d?.name }}</option>
                                            </select>
                                            <ErrorMessage :message="form.errors.blood_type"/>
                                        </div>
                                    </div>
                                  
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Employee ID:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="Employee ID" 
                                                autocomplete="off"
                                                :class="form.errors.employee_id ? 'error-field' : ''"
                                                v-model="form.employee_id"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.employee_id"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Employee Type:</label>
                                        <div class="col-sm-7">
                                            <select 
                                                class="form-select" 
                                                aria-label="Select Employee Type"
                                                :class="form.errors.employee_type ? 'error-field' : ''"
                                                v-model="form.employee_type"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                                <option value="" hidden>
                                                    Select Employee Type
                                                </option>
                                                <option
                                                v-for="(
                                                        d, i
                                                    ) in employeeTypes"
                                                    :key="d.id"
                                                    :value="d.id"
                                                    :selected="
                                                        form.employee_type ==
                                                        d?.id
                                                    "
                                                
                                                >{{ d?.name }}</option>
                                            </select>
                                            <ErrorMessage :message="form.errors.employee_type"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Department:</label>
                                        <div class="col-sm-7">
                                            <select 
                                                class="form-select" 
                                                aria-label="Select Department"
                                                :class="form.errors.department ? 'error-field' : ''"
                                                v-model="form.department"
                                                @change="getPositionBasedOnDepartment($event)"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                                <option value="" hidden>
                                                    Select Department
                                                </option>
                                                <option 
                                                    v-for="(
                                                        d, i
                                                    ) in departments"
                                                    :key="d.id"
                                                    :value="d.id"
                                                    :selected="
                                                        form.department ==
                                                        d?.id
                                                    "
                                                >{{ d?.name }}</option>
                                            </select>
                                            <ErrorMessage :message="form.errors.department"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Position:</label>
                                        <div class="col-sm-7">
                                            <select 
                                                class="form-select" 
                                                aria-label="Select Position"
                                                :class="form.errors.position ? 'error-field' : ''"
                                                v-model="form.position"
                                                @change="getBasicSalary($event)"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                                <option value="" hidden>
                                                    Select Position
                                                </option>
                                                <option 
                                                    v-for="(
                                                        d, i
                                                    ) in designations"
                                                    :key="d.id"
                                                    :value="d.id"
                                                    :selected="
                                                        form.position ==
                                                        d?.id
                                                    "
                                                >{{ d?.name }}</option>
                                            </select>
                                            <ErrorMessage :message="form.errors.position"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mt-1">
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Date Hired:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="date" 
                                                class="form-control" 
                                                placeholder="Date Hired" 
                                                autocomplete="off"
                                                :class="form.errors.date_hired ? 'error-field' : ''"
                                                v-model="form.date_hired"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.date_hired"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Salary Rate:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control text-end" 
                                                @keypress="isNumber($event)" 
                                                @blur="
                                                    converToCurrency(
                                                        $event
                                                    )
                                                "
                                                placeholder="Salary Rate" 
                                                autocomplete="off"
                                                :class="form.errors.rate ? 'error-field' : ''"
                                                v-model="form.rate"
                                                readonly
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.rate"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">SSS:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="SSS" 
                                                autocomplete="off"
                                                :class="form.errors.sss ? 'error-field' : ''"
                                                v-model="form.sss"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.sss"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">TIN:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="TIN" 
                                                autocomplete="off"
                                                :class="form.errors.tin ? 'error-field' : ''"
                                                v-model="form.tin"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.tin"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">Pag-Ibig:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="Pag-Ibig" 
                                                autocomplete="off"
                                                :class="form.errors.pag_ibig ? 'error-field' : ''"
                                                v-model="form.pag_ibig"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.pag_ibig"/>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-5 col-form-label">PhilHealth:</label>
                                        <div class="col-sm-7">
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="PhilHealth" 
                                                autocomplete="off"
                                                :class="form.errors.philhealth ? 'error-field' : ''"
                                                v-model="form.philhealth"
                                                :disabled="modalAttrs?.action == 'VIEW'"
                                            >
                                            <ErrorMessage :message="form.errors.philhealth"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mt-1" v-if="form.id">
                                    <span><b>Status:</b> {{ form.terminate == 'N' ? 'Active' : 'Terminated' }}</span>
                                </div>
                                <div class="col-4 mt-1" v-if="form.terminate == 'Y'">
                                    <span><b>Reason:</b> {{ form.reason }}</span>
                                </div>
                                <div class="col-4 mt-1" v-if="form.terminate == 'Y'">
                                    <span><b>Termination Date:</b> {{ form.termination_date }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                  </div>
                  <div class="tab-pane fade" id="v-pills-family-background" role="tabpanel" aria-labelledby="v-pills-family-background-tab">
                    <div class="card" style="width:1050px; height: 410px; overflow: auto;">
                        <div class="card-body">
                            <h5 class="card-title">Employee Family Background</h5>
                            <div class="row">
                                <div class="col-12 text-end pb-2" v-if="modalAttrs?.action != 'VIEW'"><button type="button" class="btn btn-primary btn-sm" @click="addNewFamilyBackgoundRow()">Add Family Background</button></div>
                                <div class="col-12 mt-1">
                                    <table class="table table-bordered table-sm display w-100" width="100%">
                                        <thead>
                                            <tr>
                                                <th scope="col" width="20%">Name</th>
                                                <th scope="col" width="15%">Relationship</th>
                                                <th scope="col" width="15%">Address</th>
                                                <th scope="col" width="15%">Contact No.</th>
                                                <th scope="col" width="15%">Birthday</th>
                                                <th scope="col" width="15%">Occupation</th>
                                                <th scope="col" width="5%" v-if="modalAttrs?.action != 'VIEW'">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    d, i
                                                ) in form.familyBackgound"
                                                :key="i"
                                                v-if="form.familyBackgound.length"
                                            >
                                                <th>
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="name" 
                                                        autocomplete="off"
                                                        v-model="d.name"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`familyBackgound.${i}.name`]?.replace(`familyBackgound.${i}.name`, 'name')"/>
                                                </th>
                                                <td>
                                                    <select 
                                                        class="form-select" 
                                                        aria-label="Select Relationship"
                                                        v-model="d.relationship"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                        <option value="" hidden>
                                                            Select Relationship
                                                        </option>
                                                        <option 
                                                            v-for="(
                                                                data, index
                                                            ) in relationshipTypes"
                                                            :key="data.id"
                                                            :value="data.id"
                                                            :selected="
                                                                d.relationship ==
                                                                data?.id
                                                            "
                                                        >{{ data?.name }}</option>
                                                    </select>
                                                    <ErrorMessage :message="form.errors[`familyBackgound.${i}.relationship`]?.replace(`familyBackgound.${i}.relationship`, 'relationship')"/>
                                                </td>
                                                <td>
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Address" 
                                                        autocomplete="off"
                                                        v-model="d.address"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`familyBackgound.${i}.address`]?.replace(`familyBackgound.${i}.address`, 'address')"/>
                                                </td>
                                                <td>
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Contact No." 
                                                        autocomplete="off"
                                                        v-model="d.contact_no"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`familyBackgound.${i}.contact_no`]?.replace(`familyBackgound.${i}.contact_no`, 'contact no.')"/>
                                                </td>
                                                <td>
                                                    <input 
                                                        type="date" 
                                                        class="form-control" 
                                                        placeholder="Birthday" 
                                                        autocomplete="off"
                                                        v-model="d.birthdate"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`familyBackgound.${i}.birthdate`]?.replace(`familyBackgound.${i}.birthdate`, 'birthdate')"/>
                                                </td>
                                                <td>
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Occupation" 
                                                        autocomplete="off"
                                                        v-model="d.occupation"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`familyBackgound.${i}.occupation`]?.replace(`familyBackgound.${i}.occupation`, 'occupation')"/>
                                                </td>
                                                <td class="text-center" width="5%" v-if="modalAttrs?.action != 'VIEW'">
                                                    <a 
                                                        href="javascript:;" 
                                                        class="delete-btn-option red" 
                                                        title="Delete" 
                                                        @click="btnActionDelFamilyBackgoundRow(i, d)"
                                                        ><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                            <tr v-else>
                                                <td :colspan="modalAttrs?.action != 'VIEW' ? 7 : 6" class="text-center">No Data Results.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="tab-pane fade" id="v-pills-work-experience" role="tabpanel" aria-labelledby="v-pills-work-experience-tab">
                    <div class="card" style="width:1050px; height: 410px; overflow: auto;">
                        <div class="card-body">
                            <h5 class="card-title">Employee Work Experience</h5>
                            <div class="row">
                                <div class="col-12 text-end pb-2" v-if="modalAttrs?.action != 'VIEW'"><button type="button" class="btn btn-primary btn-sm" @click="addNewWorkExperienceRow()">Add Work Experience</button></div>
                                <div class="col-12 mt-1">
                                    <table class="table table-bordered table-sm display w-100" width="100%">
                                        <thead>
                                        <tr>
                                            <th scope="col" width="35%">Company Name</th>
                                            <th scope="col" width="20%">Position</th>
                                            <th scope="col" width="20%">Date From</th>
                                            <th scope="col" width="20%">Date To</th>
                                            <th scope="col" width="5%" v-if="modalAttrs?.action != 'VIEW'">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr
                                            v-for="(
                                                d, i
                                            ) in form.workExperience"
                                            :key="i"
                                            v-if="form.workExperience.length"
                                        >
                                            <th>
                                                <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Company Name" 
                                                        autocomplete="off"
                                                        v-model="d.company_name"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`workExperience.${i}.company_name`]?.replace(`workExperience.${i}.company_name`, 'company name')"/>
                                            </th>
                                            <td>
                                            
                                                <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Position" 
                                                        autocomplete="off"
                                                        v-model="d.position"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`workExperience.${i}.position`]?.replace(`workExperience.${i}.position`, 'position')"/>
                                            </td>
                                            <td>
                                            
                                                <input 
                                                        type="date" 
                                                        class="form-control" 
                                                        placeholder="Date From" 
                                                        autocomplete="off"
                                                        v-model="d.date_from"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`workExperience.${i}.date_from`]?.replace(`workExperience.${i}.date_from`, 'date from')"/>
                                            </td>
                                            <td>
                                                <input 
                                                        type="date" 
                                                        class="form-control" 
                                                        placeholder="Date To" 
                                                        autocomplete="off"
                                                        v-model="d.date_to"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`workExperience.${i}.date_to`]?.replace(`workExperience.${i}.date_to`, 'date to')"/>
                                            </td>
                                            <td class="text-center" width="5%" v-if="modalAttrs?.action != 'VIEW'">
                                                    <a 
                                                        href="javascript:;" 
                                                        class="delete-btn-option red" 
                                                        title="Delete" 
                                                        @click="btnActionDelWorkExperienceRow(i, d)"
                                                        ><i class="bi bi-trash"></i></a>
                                                </td>
                                        </tr>
                                        <tr v-else>
                                                <td :colspan="modalAttrs?.action != 'VIEW' ? 5 : 4" class="text-center">No Data Results.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="tab-pane fade" id="v-pills-educational-attainment" role="tabpanel" aria-labelledby="v-pills-educational-attainment-tab">
                    <div class="card" style="width:1050px; height: 410px; overflow: auto;">
                        <div class="card-body">
                            <h5 class="card-title">Employee Educational Attainment</h5>
                            <div class="row">
                                <div class="col-12 text-end pb-2" v-if="modalAttrs?.action != 'VIEW'"><button type="button" class="btn btn-primary btn-sm" @click="addNewEducationalAttainmentRow()">Add Educational Attainment</button></div>
                                <div class="col-12 mt-1">
                                    <table class="table table-bordered table-sm display w-100" width="100%">
                                        <thead>
                                        <tr>
                                            <th scope="col" width="35%">Name of School</th>
                                            <th scope="col" width="20%">Address</th>
                                            <th scope="col" width="20%">School Level</th>
                                            <th scope="col" width="10%">Year From</th>
                                            <th scope="col" width="10%">Year To</th>
                                            <th scope="col" width="5%" v-if="modalAttrs?.action != 'VIEW'">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr
                                        v-for="(
                                                                d, i
                                                            ) in form.educationalAttainment"
                                                            :key="i"
                                        v-if="form.educationalAttainment.length"
                                        >
                                            <th>
                                                <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="School Name" 
                                                        autocomplete="off"
                                                        v-model="d.school_name"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`educationalAttainment.${i}.school_name`]?.replace(`educationalAttainment.${i}.school_name`, 'school name')"/>
                                            </th>
                                            <td>
                                                <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        placeholder="Address" 
                                                        autocomplete="off"
                                                        v-model="d.address"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                    <ErrorMessage :message="form.errors[`educationalAttainment.${i}.address`]?.replace(`educationalAttainment.${i}.address`, 'address')"/>
                                            </td>
                                            <td>
                                                <select 
                                                        class="form-select" 
                                                        aria-label="Select Educational Level"
                                                        v-model="d.school_level"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                        <option value="" hidden>
                                                            Select Educational Level
                                                        </option>
                                                        <option 
                                                            v-for="(
                                                                data, index
                                                            ) in educationalLevels"
                                                            :key="data.id"
                                                            :value="data.id"
                                                            :selected="
                                                                d.school_level ==
                                                                data?.id
                                                            "
                                                        >{{ data?.name }}</option>
                                                    </select>
                                                    <ErrorMessage :message="form.errors[`educationalAttainment.${i}.school_level`]?.replace(`educationalAttainment.${i}.school_level`, 'school level')"/>
                                            </td>
                                            <td>

                                                    <select 
                                                        class="form-select" 
                                                        aria-label="From"
                                                        v-model="d.year_from"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                        <option value="" hidden>
                                                            From
                                                        </option>
                                                        <option 
                                                            v-for="(
                                                                data, index
                                                            ) in years"
                                                            :key="data"
                                                            :value="data"
                                                            :selected="
                                                                d.year_from ==
                                                                data
                                                            "
                                                        >{{ data }}</option>
                                                    </select>
                                                    <ErrorMessage :message="form.errors[`educationalAttainment.${i}.year_from`]?.replace(`educationalAttainment.${i}.year_from`, 'year from')"/>
                                            </td>

                                            <td>
                                                    <select 
                                                        class="form-select" 
                                                        aria-label="To"
                                                        v-model="d.year_to"
                                                        :disabled="modalAttrs?.action == 'VIEW'"
                                                    >
                                                        <option value="" hidden>
                                                            To
                                                        </option>
                                                        <option 
                                                            v-for="(
                                                                data, index
                                                            ) in years"
                                                            :key="data"
                                                            :value="data"
                                                            :selected="
                                                                d.year_to ==
                                                                data
                                                            "
                                                        >{{ data }}</option>
                                                    </select>
                                                    <ErrorMessage :message="form.errors[`educationalAttainment.${i}.year_to`]?.replace(`educationalAttainment.${i}.year_to`, 'year to')"/>
                                            </td>
                                            <td class="text-center" width="5%" v-if="modalAttrs?.action != 'VIEW'">
                                                    <a 
                                                        href="javascript:;" 
                                                        class="delete-btn-option red" 
                                                        title="Delete" 
                                                        @click="btnActionDelEducationalAttainmentRow(i, d)"
                                                    ><i class="bi bi-trash"></i></a>
                                                </td>
                                        </tr>
                                        <tr v-else>
                                                <td :colspan="modalAttrs?.action != 'VIEW' ? 6 : 5" class="text-center">No Data Results.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
              
              
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button v-if="props?.modalAttrs?.action != 'VIEW'" type="submit" class="btn btn-primary" :disabled="form.processing">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</template>
