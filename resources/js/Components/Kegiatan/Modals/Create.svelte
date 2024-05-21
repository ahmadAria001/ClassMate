<script lang="ts">
    import { Button, Input, Label, Modal } from "flowbite-svelte";

    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    import axiosInstance from "axios";
    import { page } from "@inertiajs/svelte";

    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";

    import {
        type CreateSchema,
        createSchema,
    } from "@R/Utils/Schema/RequestDoc/Create";
    import { createEventDispatcher, onMount } from "svelte";
    import { twMerge } from "tailwind-merge";

    export let showState = false;

    const dispatch = createEventDispatcher();
    const axios = axiosInstance.create({ withCredentials: true });

    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const { form, isSubmitting, errors } = createForm<CreateSchema>({
        extend: validator<CreateSchema>({
            schema: createSchema,
        }),
        onSubmit: async (values) => {
            let body = {
                description: values.description,
                request_by: values.request_by,
                _token: $page.props.csrf_token,
            };

            const response = await axios.post("/api/docs/request", body);
            console.log(response.data);

            err = response.data;
            showState = false;
            dispatch("comp");

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        },
        onError: (values: unknown) => {
            err = {
                message: values?.response?.data?.message,
                status: values?.response?.data?.status,
            };
            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            console.error(values);

            return;
        },
        onSuccess: () => {
            console.log("Success");
        },
    });
</script>

<Modal title="Tambah Kegiatan" bind:open={showState} autoclose>
    <form method="POST">
        <div class="grid md:grid-cols-3 md:gap-6">
            <div class="mb-5">
                <Label for="activityName" class="mb-3">Nama Kegiatan</Label>
                <Input id="activityName" placeholder="Nama Kegiatan" />
            </div>
            <div class="mb-5">
                <Label for="location" class="mb-3">Lokasi</Label>
                <Input id="location" placeholder="Lokasi" />
            </div>
        </div>
        <div class="grid md:grid-cols-3 md:gap-6">
            <div class="mb-5">
                <Label for="time" class="mb-3">Waktu</Label>
                <Input id="time" placeholder="Waktu" />
            </div>
            <div class="mb-5">
                <Label for="date" class="mb-3">Tanggal</Label>
                <Input type="date" id="date" placeholder="Tanggal" />
            </div>
        </div>
        <div class="block flex">
            <Button type="submit" class="ml-auto">Simpan</Button>
        </div>
    </form>
</Modal>
