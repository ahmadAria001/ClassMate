<script lang="ts">
    import axiosInstance from "axios";

    import {
        Modal,
        Input,
        Label,
        Button,
        Select,
        Toggle,
        Toast,
    } from "flowbite-svelte";
    import {
        CheckCircleSolid,
        CloseCircleSolid,
        ExclamationCircleOutline,
    } from "flowbite-svelte-icons";

    import { page } from "@inertiajs/svelte";

    import { createForm } from "felte";
    import { validateSchema, validator } from "@felte/validator-zod";

    import {
        type UpdateSchema,
        updateSchema,
    } from "./../../../Utils/Schema/Civils/Update";
    import { twMerge } from "tailwind-merge";

    export let showState = false;
    export let submitValue:any | null = null

    const axios = axiosInstance.create({ withCredentials: true });
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const { form, isSubmitting, errors } = createForm<UpdateSchema>({
        extend: validator<UpdateSchema>({
            schema: updateSchema,
        }),
        onSubmit: async (values) => {
            const response = await axios.put("/api/civilian", {
                id: values.id,
                nik: values.nik,
                birthdate: Date.parse(values.birthdate) / 1000,
                birthplace: values.birthplace,
                residentstatus: values.residentstatus,
                fullName: values.fullName,
                nkk: values.nkk,
                rt_id: values.rt_id,
                address: values.address,
                status: values.status,
                phone: values.phone.toString(),
                religion: values.religion,
                job: values.job,
                isFamilyHead: values.isFamilyHead,
                _token: $page.props.csrf_token,
            });
            console.log(response.data);

            err = response.data;
            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        },
        onError: (values: unknown) => {
            console.error(values?.response);
            err = values?.response?.data;

            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            return;
        },
        onSuccess: (response) => {
            console.log("Success");
        },
    });

    let delReasons = [
        { value: "pindah", name: "Pindah" },
        { value: "meninggal", name: "Meninggal" },
    ];
</script>

<Modal bind:open={showState} size="sm" autoclose>
    <div class="text-center">
        <ExclamationCircleOutline
            class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
        />
        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
            Apakah yakin ingin menghapus warga ini?
        </h3>
        <Button color="red" class="me-2">Ya, yakin</Button>
        <Button color="alternative">Tidak, batal</Button>
    </div>
</Modal>
