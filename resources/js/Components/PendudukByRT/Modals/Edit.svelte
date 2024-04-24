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
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    import { page } from "@inertiajs/svelte";

    import { createForm } from "felte";
    import { validateSchema, validator } from "@felte/validator-zod";

    import {
        type UpdateSchema,
        updateSchema,
    } from "./../../../Utils/Schema/Civils/Update";
    import { twMerge } from "tailwind-merge";

    export let showState = false;
    export let data: any | null = null;

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

    let statusList = [
        { value: "ContractResident", name: "Kontrak" },
        { value: "PermanentResident", name: "Penduduk Tetap" },
        { value: "Kos", name: "Kos" },
    ];

    let civStat = [
        { value: "Aktif", name: "Aktif" },
        { value: "Meninggal", name: "Meninggal" },
        { value: "Pindah", name: "Pindah" },
    ];
</script>

<Modal title="Edit Data Warga" bind:open={showState}>
    <form method="post" use:form>
        <div class="mb-4">
            <Label for="full_name" class="mb-2">Nama Lengkap</Label>
            <Input
                id="full_name"
                name="fullName"
                placeholder="Nama Lengkap"
                value={data.data.fullName}
            />
            {#if $errors.fullName}
                <span class="text-sm text-red-500">{$errors.fullName}</span>
            {/if}
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="kk" class="mb-2">No KK</Label>
                <Input
                    id="kk"
                    name="nkk"
                    placeholder="No KK"
                    value={data.data.nkk}
                />
                {#if $errors.nkk}
                    <span class="text-sm text-red-500">{$errors.nkk}</span>
                {/if}
            </div>
            <div class="mb-4">
                <Label for="nik" class="mb-2">NIK</Label>
                <Input
                    id="nik"
                    name="nik"
                    placeholder="NIK"
                    value={data.data.nik}
                />
                {#if $errors.nik}
                    <span class="text-sm text-red-500">{$errors.nik}</span>
                {/if}
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="religion" class="mb-2">Agama</Label>
                <Input
                    id="religion"
                    placeholder="Agama"
                    name="religion"
                    value={data.data.religion}
                />
                {#if $errors.religion}
                    <span class="text-sm text-red-500">{$errors.religion}</span>
                {/if}
            </div>
            <div class="mb-4">
                <Label class="mb-2 w-full text-center"
                    >Tempat, Tanggal Lahir</Label
                >
                <div class="flex w-full gap-5">
                    <div class="flex w-full gap-5">
                        <Input
                            id="birthplace"
                            name="birthplace"
                            placeholder="Tempat Lahir"
                            value={data.data.birthplace}
                        />
                        {#if $errors.birthplace}
                            <span class="text-sm text-red-500"
                                >{$errors.birthplace}</span
                            >
                        {/if}

                        <Input
                            id="birthdate"
                            name="birthdate"
                            placeholder="Tanggal Lahir"
                            type="date"
                            pattern="\d{4}-\d{2}-\d{2}"
                            value={new Date(data.data.birthdate * 1000)
                                .toISOString()
                                .substring(0, 10)}
                        />
                        {#if $errors.birthdate}
                            <span class="text-sm text-red-500"
                                >{$errors.birthdate}</span
                            >
                        {/if}
                    </div>
                </div>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="address" class="mb-2">Alamat</Label>
                <Input
                    id="address"
                    name="address"
                    placeholder="Alamat"
                    value={data.data.address}
                />
                {#if $errors.address}
                    <span class="text-sm text-red-500">{$errors.address}</span>
                {/if}
            </div>
            <div class="mb-4">
                <Label for="no_hp" class="mb-2">No. HP</Label>
                <Input
                    type="number"
                    id="no_hp"
                    name="phone"
                    placeholder="No. HP"
                    value={data.data.phone}
                />
                {#if $errors.phone}
                    <span class="text-sm text-red-500">{$errors.phone}</span>
                {/if}
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="residentstatus" class="mb-2"
                    >Status Kependudukan</Label
                >
                <Select
                    class="my-2"
                    items={statusList}
                    placeholder="Pilih Status Penduduk"
                    size="sm"
                    name="residentstatus"
                    id="residentstatus"
                    value={data.data.residentstatus}
                />
                {#if $errors.residentstatus}
                    <span class="text-sm text-red-500"
                        >{$errors.residentstatus}</span
                    >
                {/if}
            </div>
            <div class="mb-4">
                <Label for="job" class="mb-2">Pekerjaan</Label>
                <Input
                    id="job"
                    name="job"
                    placeholder="Pekerjaan"
                    value={data.data.job}
                />
                {#if $errors.job}
                    <span class="text-sm text-red-500">{$errors.job}</span>
                {/if}
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="status" class="mb-2">Status</Label>
                <Select
                    class="my-2"
                    items={civStat}
                    size="sm"
                    id="status"
                    placeholder="Status"
                    name="status"
                    value={data.data.status}
                />

                {#if $errors.status}
                    <span class="text-sm text-red-500">{$errors.status}</span>
                {/if}
            </div>
            <div
                class={twMerge(
                    "mb-4",
                    $page.props.auth.user.role == "Admin" ? "" : "hidden",
                )}
            >
                <Label for="rt" class="mb-2">RT</Label>
                {#if $page.props.auth.user.role == "Admin"}
                    <Input
                        id="rt"
                        placeholder="Pekerjaan"
                        name="rt_id"
                        value={$page.props.auth.user.rt_id}
                    />
                {:else}
                    <Input
                        id="rt"
                        placeholder="Pekerjaan"
                        name="rt_id"
                        readonly
                        value={$page.props.auth.user.rt_id}
                    />
                {/if}
                {#if $errors.rt_id}
                    <span class="text-sm text-red-500">{$errors.rt_id}</span>
                {/if}
            </div>
        </div>
        <div class="mb-4">
            <Label for="famhead" class="mb-2">Kepala Keluarga</Label>
            <Toggle
                id="famhead"
                placeholder="Status"
                name="isFamilyHead"
                checked={data.data.isFamilyHead}
            />
            {#if $errors.isFamilyHead}
                <span class="text-sm text-red-500">{$errors.isFamilyHead}</span>
            {/if}
        </div>
        <div class="mb-4 hidden">
            <Label for="instance_id" class="mb-2">ID</Label>
            <Input
                id="instance_id"
                name="id"
                readonly
                placeholder="ID"
                value={data.data.id}
            />
        </div>

        <div class="flex justify-end">
            <Button type="submit" class="ml-auto" disabled={$isSubmitting}
                >Simpan</Button
            >
        </div>
    </form>
</Modal>

{#if err.status != null && err.status == true}
    <Toast color="green" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CheckCircleSolid class="w-5 h-5" />
            <span class="sr-only">Check icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
{#if err.status != null && err.status == false}
    <Toast color="red" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CloseCircleSolid class="w-5 h-5" />
            <span class="sr-only">Error icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
