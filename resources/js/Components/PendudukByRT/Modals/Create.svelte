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
        type CreateSchema,
        createSchema,
    } from "./../../../Utils/Schema/Civils/Create";
    import { twMerge } from "tailwind-merge";
    import { createEventDispatcher } from "svelte";

    export let showState = false;

    const dispatch = createEventDispatcher();
    const axios = axiosInstance.create({ withCredentials: true });
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    let resstatval: string | null = null;

    const { form, isSubmitting, errors } = createForm<CreateSchema>({
        extend: validator<CreateSchema>({
            schema: createSchema,
        }),
        onSubmit: async (values) => {
            // console.log(values);

            const response = await axios.post("/api/civilian", {
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

            return;
        },
        onSuccess: () => {
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

    let selectedReligion: string;
    let religion = [
        { value: "Islam", name: "Islam" },
        { value: "Protestan", name: "Protestan" },
        { value: "Katolik", name: "Katolik" },
        { value: "Hindu", name: "Hindu" },
        { value: "Budha", name: "Budha" },
        { value: "Khonghucu", name: "Khonghucu" },
    ];

    let selectedJob: string;
    let jobs = [
        { value: "Pengangguran", name: "Pengangguran" },
        { value: "Pegawai Negeri", name: "Pegawai Negeri" },
        { value: "Swasta", name: "Swasta" },
        { value: "Wiraswasta", name: "Wiraswasta" },
        { value: "Wirausaha", name: "Wirausaha" },
        { value: "Pelajar", name: "Pelajar" },
    ];
</script>

<Modal title="Tambah Warga" bind:open={showState}>
    <form method="POST" use:form>
        <div class="mb-4">
            <Label for="full_name" class="mb-2">Nama Lengkap</Label>
            <Input id="full_name" name="fullName" placeholder="Nama Lengkap" />
            {#if $errors.fullName}
                <span class="text-sm text-red-500">{$errors.fullName}</span>
            {/if}
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="kk" class="mb-2">No KK</Label>
                <Input id="kk" name="nkk" placeholder="No KK" />
                {#if $errors.nik}
                    <span class="text-sm text-red-500">{$errors.nik}</span>
                {/if}
            </div>
            <div class="mb-4">
                <Label for="nik" class="mb-2">NIK</Label>
                <Input id="nik" placeholder="NIK" name="nik" />
                {#if $errors.nik}
                    <span class="text-sm text-red-500">{$errors.nik}</span>
                {/if}
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="religion" class="mb-2">Agama</Label>
                <!-- <Input id="religion" placeholder="Agama" name="religion" /> -->
                <Select
                    id="religion"
                    items={religion}
                    bind:value={selectedReligion}
                    placeholder="Agama"
                    name="religion"
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
                    <div class="w-1/2 block h-fit">
                        <Input
                            id="birthplace"
                            placeholder="Tempat Lahir"
                            name="birthplace"
                        />
                        {#if $errors.birthplace}
                            <span class="text-sm text-red-500"
                                >{$errors.birthplace}</span
                            >
                        {/if}
                    </div>
                    <div class="w-1/2 block h-fit">
                        <Input
                            type="date"
                            id="birthdate"
                            placeholder="Tanggal Lahir"
                            name="birthdate"
                            pattern="\d{4}-\d{2}-\d{2}"
                        />
                        {#if $errors.birthdate}
                            <span class="text-sm text-red-500 w-full text-end"
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
                <Input id="address" placeholder="Alamat" name="address" />
                {#if $errors.address}
                    <span class="text-sm text-red-500">{$errors.address}</span>
                {/if}
            </div>
            <div class="mb-4">
                <Label for="phone" class="mb-2">No. HP</Label>
                <Input
                    type="number"
                    id="phone"
                    placeholder="No. HP"
                    name="phone"
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
                    bind:value={resstatval}
                    items={statusList}
                    class="my-2"
                    placeholder="Pilih Status Penduduk"
                    name="residentstatus"
                    id="residentstatus"
                />
                <!-- bind:value={selectedReason} -->

                {#if $errors.residentstatus}
                    <span class="text-sm text-red-500"
                        >{$errors.residentstatus}</span
                    >
                {/if}
            </div>
            <div class="mb-4">
                <Label for="job" class="mb-2">Pekerjaan</Label>
                <!-- <Input id="job" placeholder="Pekerjaan" name="job" /> -->
                <Select
                    id="job"
                    items={jobs}
                    bind:value={selectedJob}
                    placeholder="Pekerjaan"
                    name="job"
                />
                {#if $errors.job}
                    <span class="text-sm text-red-500">{$errors.job}</span>
                {/if}
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
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
                        placeholder="RT"
                        name="rt_id"
                        value={$page.props.auth.user.rt_id}
                    />
                {:else}
                    <Input
                        id="rt"
                        placeholder="RT"
                        name="rt_id"
                        readonly
                        value={$page.props.auth.user.rt_id}
                    />
                {/if}
                {#if $errors.rt_id}
                    <span class="text-sm text-red-500">{$errors.rt_id}</span>
                {/if}
            </div>
            <div class="mb-4">
                <Label for="status" class="mb-2">Status</Label>
                <Select
                    class="my-2"
                    items={civStat}
                    id="status"
                    placeholder="Status"
                    name="status"
                />

                {#if $errors.status}
                    <span class="text-sm text-red-500">{$errors.status}</span>
                {/if}
            </div>
        </div>
        <div class="mb-4 w-fit">
            <Label for="famhead" class="mb-2">Kepala Keluarga</Label>
            {#if resstatval == "Kos" || !resstatval}
                <Toggle
                    id="famhead"
                    onclick="return false"
                    placeholder="Status"
                    name="isFamilyHead"
                    class="w-fit"
                />
            {:else}
                <Toggle
                    id="famhead"
                    placeholder="Status"
                    name="isFamilyHead"
                    class="w-fit"
                />
            {/if}
            {#if $errors.isFamilyHead}
                <span class="text-sm text-red-500">{$errors.isFamilyHead}</span>
            {/if}
            <span class="w-full text-sm text-gray-600"
                >*Pilih Status Kependudukan <br /> Sebelum Memutuskan Status Kepala
                Keluarga</span
            >
        </div>

        <div class="flex justify-end">
            <Button type="submit" class="w-1/5" disabled={$isSubmitting}
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
