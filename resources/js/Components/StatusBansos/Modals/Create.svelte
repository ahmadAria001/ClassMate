<script lang="ts">
    import {
        Button,
        Input,
        Label,
        Modal,
        Radio,
        Select,
        Textarea,
        Toast,
    } from "flowbite-svelte";

    import axiosInstance from "axios";
    import { page } from "@inertiajs/svelte";

    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";

    import {
        type CreateSchema,
        createSchema,
    } from "@R/Utils/Schema/SocialAssistList/Create";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";
    import { createEventDispatcher } from "svelte";

    export let showState = false;

    const axios = axiosInstance.create({ withCredentials: true });
    const dispatch = createEventDispatcher();

    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const { form, isSubmitting, errors } = createForm<CreateSchema>({
        extend: validator<CreateSchema>({
            schema: createSchema,
        }),
        onSubmit: async (values) => {
            try {
                let body;

                body = {
                    request_by: $page.props.auth.user.id,
                    childrens: values.jumlah_tanggungan,
                    salary: values.pendapatan_bulanan,
                    expenses: values.pengeluaran_bulanan,
                    job_status: values.status_pekerjaan,
                    residence_status: values.status_tempat_tinggal,
                    _token: $page.props.csrf_token,
                };

                // masih dummy
                const response = await axios.post("/api/bansos", body);

                err = response.data;
                showState = false;
                dispatch("comp");

                setTimeout(() => {
                    err = { status: null, message: null };
                }, 5000);
            } catch (error) {
                err = {
                    message: error?.response?.data?.message,
                    status: error?.response?.data?.status,
                };
                showState = false;
                setTimeout(() => {
                    err = { status: null, message: null };
                }, 5000);

                console.error(error);

                return;
            }
        },
        onError: (values) => {
            err = {
                message: values?.response?.data?.message,
                status: values?.response?.data?.status,
            };
            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            console.error(values);
        },
        onSuccess: () => {
            console.log("Success");
        },
    });

    const fullName = $page.props.auth.user.fullName;

    // benefit
    let tanggunganOptions = [
        { value: 1, label: "0 Anak" },
        { value: 2, label: "1 Anak" },
        { value: 3, label: "2 Anak" },
        { value: 4, label: "3 Anak" },
        { value: 5, label: ">3 Anak" },
    ];

    // cost
    let pendapatanBulanans = [
        { value: 5, label: "<= Rp. 1.000.000" },
        { value: 4, label: "<= Rp. 1.500.000" },
        { value: 3, label: "<= Rp. 2.500.000" },
        { value: 2, label: "<= Rp. 3.500.000" },
        { value: 1, label: "> Rp. 3.500.000" },
    ];

    // benefit
    let pengeluaranBulanans = [
        { value: 1, label: "<= Rp. 750.000" },
        { value: 2, label: "<= Rp. 1.500.000" },
        { value: 3, label: "<= Rp. 2.000.000" },
        { value: 4, label: "<= Rp. 2.500.000" },
        { value: 5, label: "> Rp. 2.500.000" },
    ];

    // benefit
    let selectedJob: number;
    let jobs = [
        { value: 3, name: "Karyawan" },
        { value: 4, name: "Wirausaha" },
        { value: 5, name: "Tidak Bekerja" },
    ];

    // benefit
    let selectedResidence: number;
    let residences = [
        { value: 3, name: "Warisan Keluarga" },
        { value: 4, name: "Milik Sendiri" },
        { value: 5, name: "Kontrak" },
    ];
</script>

<Modal title="Buat Pengajuan Bantuan Sosial" bind:open={showState}>
    <form use:form>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label class="mb-2">Nama Lengkap</Label>
                <Input
                    id="fullName"
                    name="full_name"
                    placeholder="Masukan Nama"
                    value={fullName}
                    readonly
                />
            </div>
            <div class="mb-4">
                <Label class="mb-2">Jumlah Tanggungan</Label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        {#each tanggunganOptions.slice(0, 2) as option}
                            <Radio name="jumlah_tanggungan" value={option.value}
                                >{option.label}</Radio
                            >
                        {/each}
                    </div>
                    <div>
                        {#each tanggunganOptions.slice(2) as option}
                            <Radio name="jumlah_tanggungan" value={option.value}
                                >{option.label}</Radio
                            >
                        {/each}
                    </div>
                </div>
                {#if $errors.jumlah_tanggungan}
                    <span class="text-sm text-red-500"
                        >{$errors.jumlah_tanggungan}</span
                    >
                {/if}
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label class="mb-2">Pendapatan Bulanan</Label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        {#each pendapatanBulanans.slice(0, 3) as option}
                            <Radio
                                name="pendapatan_bulanan"
                                value={option.value}>{option.label}</Radio
                            >
                        {/each}
                    </div>
                    <div>
                        {#each pendapatanBulanans.slice(3) as option}
                            <Radio
                                name="pendapatan_bulanan"
                                value={option.value}>{option.label}</Radio
                            >
                        {/each}
                    </div>
                </div>

                {#if $errors.pendapatan_bulanan}
                    <span class="text-sm text-red-500"
                        >{$errors.pendapatan_bulanan}</span
                    >
                {/if}
            </div>
            <div class="mb-4">
                <Label class="mb-2">Pengeluaran Bulanan</Label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        {#each pengeluaranBulanans.slice(0, 3) as option}
                            <Radio
                                name="pengeluaran_bulanan"
                                value={option.value}>{option.label}</Radio
                            >
                        {/each}
                    </div>
                    <div>
                        {#each pengeluaranBulanans.slice(3) as option}
                            <Radio
                                name="pengeluaran_bulanan"
                                value={option.value}>{option.label}</Radio
                            >
                        {/each}
                    </div>
                </div>

                {#if $errors.pengeluaran_bulanan}
                    <span class="text-sm text-red-500"
                        >{$errors.pengeluaran_bulanan}</span
                    >
                {/if}
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label class="mb-2">Status Pekerjaan</Label>
                <Select bind:value={selectedJob} name="status_pekerjaan">
                    {#each jobs as job}
                        <option value={job.value}>{job.name}</option>
                    {/each}
                </Select>
                {#if $errors.status_pekerjaan}
                    <span class="text-sm text-red-500"
                        >{$errors.status_pekerjaan}</span
                    >
                {/if}
            </div>
            <div class="mb-4">
                <Label class="mb-2">Status Tempat Tinggal</Label>
                <Select
                    bind:value={selectedResidence}
                    name="status_tempat_tinggal"
                >
                    {#each residences as residence}
                        <option value={residence.value}>{residence.name}</option
                        >
                    {/each}
                </Select>
                {#if $errors.status_tempat_tinggal}
                    <span class="text-sm text-red-500"
                        >{$errors.status_tempat_tinggal}</span
                    >
                {/if}
            </div>
        </div>
        <div class="text-end">
            <Button type="submit" disabled={$isSubmitting}
                >Kirim Pengajuan</Button
            >
        </div>
    </form>
</Modal>

{#if err.message}
    <Toast class="bottom-4 right-4 fixed z-50">
        <div
            class={`inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg ${
                err.status
                    ? "bg-green-100 text-green-500"
                    : "bg-red-100 text-red-500"
            }`}
        >
            {#if err.status}
                <CheckCircleSolid class="h-5 w-5" />
            {:else}
                <CloseCircleSolid class="h-5 w-5" />
            {/if}
        </div>
        <div class="ml-3 text-sm font-normal">{err.message}</div>
        <Toast />
    </Toast>
{/if}
