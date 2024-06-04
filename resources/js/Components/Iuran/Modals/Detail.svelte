<script lang="ts">
    import {
        Button,
        Input,
        Label,
        Modal,
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
    } from "@R/Utils/Schema/Dues/Create";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";
    import { onMount } from "svelte";

    export let showState = false;
    export let items: string;

    let initialData: { duesName: string; duesAmount: string } = {
        duesName: "",
        duesAmount: "",
    };
    let currentData: { duesName: string; duesAmount: string } = {
        duesName: "",
        duesAmount: "",
    };

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

    const getDuesCategoryData = async (id: string = "") => {
        const response = await axios.get(
            `/api/dues/${encodeURIComponent(id)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        return response.data;
    };

    let duesCategoryData: any;
    onMount(async () => {
        const response = await getDuesCategoryData(items);
        duesCategoryData = await response.data;

        // console.log(duesCategoryData);
        initialData = {
            duesName: duesCategoryData.typeDues,
            duesAmount: duesCategoryData.amt_dues,
        };
        // console.log(
        //     "initial: " + initialData.duesAmount + " " + initialData.duesName,
        // );
        currentData = { ...initialData };
    });

    $: formChanged =
        initialData.duesName !== currentData.duesName ||
        initialData.duesAmount !== currentData.duesAmount;

    $: isDisabled = !formChanged;
</script>

<Modal
    title="Detail Kategori Iuran"
    size="xs"
    bind:open={showState}
    on:close={() => (formChanged = false)}
>
    {#if duesCategoryData}
        <form method="POST" use:form>
            <div class="mb-4">
                <Label for="duesCategoryName" class="mb-2">Nama Kategori</Label>
                <Input
                    id="duesCategoryName"
                    name="duesName"
                    placeholder="Masukan nama kategori"
                    value={duesCategoryData?.typeDues}
                    on:input={(e) => (currentData.duesName = e.target.value)}
                />
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
                    value={duesCategoryData?.amt_dues}
                    on:input={(e) => (currentData.duesAmount = e.target.value)}
                />
                {#if $errors.duesAmount}
                    <span class="text-sm text-red-500"
                        >{$errors.duesAmount}</span
                    >
                {/if}
            </div>
            <div class="text-end">
                <Button type="submit" class="mr-2" color="red">Hapus</Button>
                {#if duesCategoryData?.status}
                    <Button type="submit" class="mr-2" color="yellow"
                        >Nonaktifkan</Button
                    >
                {:else}
                    <Button type="submit" class="mr-2" color="yellow"
                        >Aktifkan</Button
                    >
                {/if}
                <Button type="submit" class="" bind:disabled={isDisabled}
                    >Ubah</Button
                >
            </div>
        </form>
    {/if}
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
