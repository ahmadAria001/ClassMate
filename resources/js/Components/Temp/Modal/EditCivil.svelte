<script lang="ts">
    import axios from "axios";
    import { twMerge } from "tailwind-merge";
    import { createForm } from "felte";
    import { z } from "zod";
    import { validator } from "@felte/validator-zod";
    import { page, router } from "@inertiajs/svelte";

    import BaseModal from "./BaseModal.svelte";
    import { UpdateCivilian } from "../../../schema";

    export let showState = false;
    export let targetId: any = null;

    let err = {
        status: false,
        message: null,
    };

    const getData = async (id: string) => {
        const response = await axios.get(`/api/civiliant/${id}`, {
            headers: {
                Accept: "*/*",
                Authorization: `Bearer ${getCookie("token")}`,
            },
        });

        return response.data;
    };

    const { form, isSubmitting, errors } = createForm<
        z.infer<typeof UpdateCivilian>
    >({
        extend: validator<z.infer<typeof UpdateCivilian>>({
            schema: UpdateCivilian,
        }),
        onSubmit: async (values) => {
            console.log(values);

            const response = await axios.put(
                "/api/family",
                {
                    id: values.id,
                    nik: values.nik,
                    birthdate: Date.parse(values.birthdate) / 1000,
                    birthplace: values.birthplace,
                    residentstatus: values.residentstatus,
                    fullName: values.fullName,
                    family_id: values.family_id,
                    _token: $page.props.csrf_token,
                },
                {
                    headers: {
                        Authorization: `Bearer ${getCookie("token")}`,
                    },
                },
            );
            console.log(response.data);

            const { status, message } = response.data;
            if (!status) {
                err = { status: true, message: message };
                setTimeout(() => {
                    err = { status: false, message: null };
                }, 5000);
            } else showState = false;
        },
        onError: (values: unknown) => {
            err = { message: values?.response?.data.message, status: true };
            return;
        },
        onSuccess: () => {
            console.log("Success");
        },
    });
</script>

<div
    class={twMerge(
        "fixed top-0 right-0 z-[101] rounded-lg items-center bg-blue-500 text-white text-sm font-bold px-4 py-3",
        err.status ? "flex" : "hidden",
    )}
    role="alert"
>
    <svg
        class="fill-current w-4 h-4 mr-2"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        ><path
            d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z"
        /></svg
    >
    <p>{err.message}</p>
</div>

<BaseModal bind:showState>
    {#if targetId}
        {#await getData(targetId)}
            <span>Loading...</span>
        {:then data}
            <form use:form method="POST">
                <div class="mb-4">
                    <label
                        for="nik"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >NIK</label
                    >
                    <input
                        type="text"
                        id="nik"
                        name="nik"
                        class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="123"
                        required
                        value={data.data.nik}
                    />
                    {#if $errors.nik}
                        <span class="text-sm text-red-500">{$errors.nik}</span>
                    {/if}
                </div>
                <div class="mb-4">
                    <label
                        for="birthdate"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >Birth Date</label
                    >
                    <input
                        type="date"
                        id="birthdate"
                        name="birthdate"
                        class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        required
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
                <div class="mb-4">
                    <label
                        for="birthplace"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >Birth Place</label
                    >
                    <input
                        type="text"
                        id="birthplace"
                        name="birthplace"
                        class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Super Earth"
                        required
                        value={data.data.birthplace}
                    />
                    {#if $errors.birthplace}
                        <span class="text-sm text-red-500"
                            >{$errors.birthplace}</span
                        >
                    {/if}
                </div>
                <div class="mb-4">
                    <label
                        for="fullName"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >Full Name</label
                    >
                    <input
                        type="text"
                        id="fullName"
                        name="fullName"
                        class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Super Earth"
                        required
                        value={data.data.fullName}
                    />
                    {#if $errors.fullName}
                        <span class="text-sm text-red-500"
                            >{$errors.fullName}</span
                        >
                    {/if}
                </div>
                <div class="mb-4">
                    <label
                        for="residentstatus"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >Resident Status</label
                    >
                    <select
                        name="residentstatus"
                        id="residentstatus"
                        class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        required
                    >
                        {#if data.data.residentstatus === "ContractResident"}
                            <option value="ContractResident" selected>
                                ContractResident
                            </option>
                            <option value="PermanentResident">
                                PermanentResident
                            </option>
                        {:else}
                            <option value="ContractResident">
                                ContractResident
                            </option>
                            <option value="PermanentResident" selected>
                                PermanentResident
                            </option>
                        {/if}
                    </select>
                    {#if $errors.residentstatus}
                        <span class="text-sm text-red-500">
                            {$errors.residentstatus}
                        </span>
                        <br />
                    {/if}
                </div>
                <div class="mb-4">
                    <label
                        for="family_id"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >Family ID</label
                    >
                    <input
                        type="family_id"
                        id="family_id"
                        name="family_id"
                        class="shadow-sm rounded-md w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="1"
                        required
                        value={data.data.family_id}
                    />
                    {#if $errors.family_id}
                        <span class="text-sm text-red-500"
                            >{$errors.family_id}</span
                        >
                        <br />
                    {/if}
                </div>
                <input
                    type="submit"
                    value="Update"
                    disabled={$isSubmitting}
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
                />
            </form>
        {/await}
    {/if}
</BaseModal>
