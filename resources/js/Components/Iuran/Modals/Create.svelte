<script lang="ts">
    import {
        Button,
        Input,
        Label,
        Modal,
        Select,
        Toast,
    } from "flowbite-svelte";

    import axiosInstance from "axios";
    import { page } from "@inertiajs/svelte";

    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";
    import {
        type CreateSchema,
        createSchema,
    } from "@R/Utils/Schema/Dues/Create";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    export let showState = false;

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
            console.log(values);
            let body: any;
            body = {
                duesName: values.duesName,
                duesAmount: values.duesAmount,
                _token: $page.props.csrf_token,
            };
            const response = await axios.post("/api/dues", body);
            console.log(response.data);

            err = response.data;
            showState = false;
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

<Modal title="Tambah Kategori Iuran" size="xs" bind:open={showState}>
    <form method="POST" use:form>
        <div class="mb-4">
            <Label for="duesCategoryName" class="mb-2">Nama Kategori</Label>
            <Select
                id="duesCategoryName"
                name="duesName"
                placeholder="Masukan nama kategori"
            >
                <!-- name="residentstatus"
                id="residentstatus" -->
                <!-- placeholder="Pilih Status Penduduk" -->
                <option value="Security">Keamanan</option>
                <option value="TrashManagement">Sampah</option>
                <option value="Event">Acara</option>
                <option value="Funeral">Kematian</option>
            </Select>
            {#if $errors.duesName}
                <span class="text-sm text-red-500">{$errors.duesName}</span>
            {/if}
        </div>
        <div class="mb-4">
            <Label for="duesAmount" class="mb-2">Jumlah Iuran</Label>
            <Input
                rows="2"
                id="duesAmount"
                name="duesAmount"
                placeholder="Masukan jumlah iuran"
            />
            {#if $errors.duesAmount}
                <span class="text-sm text-red-500">{$errors.duesAmount}</span>
            {/if}
        </div>
        <div class="block flex">
            <Button type="submit" class="ml-auto" disabled={!isSubmitting}
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
