<script lang="ts">
    import {
        Button,
        Dropdown,
        DropdownItem,
        Input,
        Label,
        Modal,
        Select,
        Toast,
    } from "flowbite-svelte";
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
    import { writable } from "svelte/store";
    const errorsDesc = writable({ description: "" });

    export let showState = false;
    let descriptionChange: string = "";

    const dispatch = createEventDispatcher();
    const axios = axiosInstance.create({ withCredentials: true });

    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };
    let ddOpen = false;
    let data: { value: number; name: string }[] | null = null;

    const getCivilis = async () => {
        const response = await axios.get("/api/civilian");

        return response.data.data;
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

    function setDescription(value: string) {
        descriptionChange = value;
        const descInput = document.getElementById(
            "problems",
        ) as HTMLInputElement;
        if (descInput) {
            descInput.value = value;
            descInput.dispatchEvent(new Event("input", { bubbles: true }));
        }
        ddOpen = false; // Close the dropdown when an item is selected
    }

    const populateOption = async () => {
        const response = await getCivilis();

        data = [];

        response.map((item: any) => {
            data?.push({
                value: item.id,
                name: `${item.fullName} | RT.${item.rt_id.number}`,
            });
        });
    };

    onMount(async () => {
        await populateOption();
    });
</script>

<Modal title="Buat Pengajuan Surat" bind:open={showState}>
    <form method="POST" use:form>
        {#if data}
            <div
                class={twMerge(
                    "mb-4",
                    $page.props.auth.user.role != "Warga"
                        ? ""
                        : "sr-only hidden",
                )}
            >
                <Label for="req" class="mb-2">Pemohon</Label>
                {#if $page.props.auth.user.role == "Warga"}
                    <Input
                        id="req"
                        name="request_by"
                        placeholder="Pemohon"
                        readonly
                        value={$page.props.auth.user.civ_id}
                    />
                {:else}
                    <Select
                        class="my-2"
                        size="sm"
                        id="req"
                        placeholder="Pemohon"
                        name="request_by"
                    >
                        {#each data as { value, name }}
                            {#if value == $page.props.auth.user.civ_id}
                                <option {value} selected>{name}</option>
                            {:else}
                                <option {value}>{name}</option>
                            {/if}
                        {/each}
                    </Select>
                {/if}

                {#if $errors.request_by}
                    <span class="text-sm text-red-500"
                        >{$errors.request_by}</span
                    >
                {/if}
            </div>

            <div class="mb-4">
                <Label for="problems" class="mb-2">Keterangan</Label>
                <Input
                    id="problems"
                    name="description"
                    placeholder="Keterangan Surat"
                    bind:value={descriptionChange}
                    on:click={() => (ddOpen = true)}
                />
                {#if $errors.description}
                    <span class="text-sm text-red-500"
                        >{$errors.description}</span
                    >
                {/if}

                <Dropdown bind:open={ddOpen}>
                    <DropdownItem
                        on:click={() =>
                            setDescription("Pengajuan Pembuatan KTP")}
                    >
                        Pengajuan Pembuatan KTP
                    </DropdownItem>
                    <DropdownItem
                        on:click={() =>
                            setDescription("Pengajuan Pembuatan KK")}
                    >
                        Pengajuan Pembuatan KK
                    </DropdownItem>
                    <DropdownItem
                        on:click={() =>
                            setDescription("Pengajuan Perubahan KTP")}
                    >
                        Pengajuan Perubahan KTP
                    </DropdownItem>
                    <DropdownItem
                        on:click={() =>
                            setDescription("Pengajuan Perubahan KK")}
                    >
                        Pengajuan Perubahan KK
                    </DropdownItem>
                </Dropdown>
            </div>
            <div class="flex w-full justify-end">
                <Button
                    type="submit"
                    class="ml-auto"
                    disabled={!isSubmitting}
                    on:click={() => {
                        descriptionChange = "";
                    }}>Kirim Pengajuan</Button
                >
            </div>
        {/if}
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
